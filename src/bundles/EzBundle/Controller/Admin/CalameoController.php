<?php

declare(strict_types=1);

namespace AlmaviaCX\Bundle\Calameo\EzBundle\Controller\Admin;

use Doctrine\DBAL\Connection;
use Ibexa\ActivityLog\REST\Input\Parser\SortClause\SortClause;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/calameo', name: 'almaviacx_calameo_admin_')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class CalameoController extends AbstractController
{
    private const string FIELD_TYPE_IDENTIFIER = 'calameo_publication';
    private const string TABLE_NAME = 'calameo_publication';

    public function __construct(
        private readonly Connection         $connection,
        private readonly ContentTypeService $contentTypeService,
        private readonly SearchService      $searchService,
        private readonly string             $APIKey,
        private readonly string             $APISecret,
        private readonly array              $config,
        private readonly bool               $deleteBookEnable,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $tableExists = $this->tableExists(self::TABLE_NAME);

        return $this->render('@EzCalameo/admin/calameo/index.html.twig', [
            'field_type_identifier' => self::FIELD_TYPE_IDENTIFIER,
            'table_name' => self::TABLE_NAME,
            'table_exists' => $tableExists,
            'configuration' => [
                'almaviacx.calameo.api.key' => $this->APIKey,
                'almaviacx.calameo.api.secret' => str_repeat('*', strlen($this->APISecret)),
                'almaviacx.calameo.http_client.config' => $this->config,
                'almaviacx.calameo.delete_book_enable' => $this->deleteBookEnable,
            ],
            'stats' => [
                'field_definitions' => $this->countCalameoFieldDefinitions(),
                'contents' => $this->countContentsWithCalameoField(),
                'publications' => $tableExists ? $this->countCalameoPublications() : null,
            ],
        ]);
    }

    #[Route('/contents', name: 'content_list', methods: ['GET'])]
    public function contentList(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = min(100, max(1, $request->query->getInt('limit', 25)));
        $offset = ($page - 1) * $limit;

        $calameoContentTypes = $this->getContentTypesWithCalameoPublicationField();

        if ($calameoContentTypes === []) {
            return $this->render('@EzCalameo/admin/calameo/content_list.html.twig', [
                'items' => [],
                'table_exists' => $this->tableExists(self::TABLE_NAME),
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => 0,
                    'pages' => 0,
                    'has_previous' => false,
                    'has_next' => false,
                ],
            ]);
        }

        $query = new Query([
            'filter' => new Criterion\ContentTypeIdentifier(
                array_column($calameoContentTypes, 'identifier')
            ),
            'limit' => $limit,
            'offset' => $offset,
        ]);
        $query->sortClauses = [new Query\SortClause\ContentId(Query::SORT_DESC)];

        $searchResult = $this->searchService->findContent($query);

        $items = [];
        $tableExists = $this->tableExists(self::TABLE_NAME);

        foreach ($searchResult->searchHits as $searchHit) {
            $content = $searchHit->valueObject;
            $contentTypeId = $content->contentInfo->contentTypeId;

            if (!isset($calameoContentTypes[$contentTypeId])) {
                continue;
            }

            foreach ($calameoContentTypes[$contentTypeId]['fields'] as $fieldIdentifier) {
                $field = $content->getField($fieldIdentifier);

                $publicationReference = null;

                if ($field !== null && $tableExists) {
                    $publicationReference = $this->getCalameoPublicationReference(
                        (int) $field->id,
                        (int) $content->versionInfo->versionNo,
                    );
                }

                $items[] = [
                    'content_id' => $content->contentInfo->id,
                    'content_name' => $content->contentInfo->name,
                    'content_type_identifier' => $calameoContentTypes[$contentTypeId]['identifier'],
                    'field_identifier' => $fieldIdentifier,
                    'field_id' => $field?->id,
                    'version' => $content->versionInfo->versionNo,
                    'publication_id' => $publicationReference['publication_id'] ?? null,
                    'folder_id' => $publicationReference['folder_id'] ?? null,
                    'content_view_url' => sprintf('/view/content/%d', $content->contentInfo->id),
                ];
            }
        }

        $total = $searchResult->totalCount;
        $pages = (int) ceil($total / $limit);

        return $this->render('@EzCalameo/admin/calameo/content_list.html.twig', [
            'items' => $items,
            'table_exists' => $tableExists,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => $pages,
                'has_previous' => $page > 1,
                'has_next' => $page < $pages,
            ],
        ]);
    }

    #[Route('/publications', name: 'publication_list', methods: ['GET'])]
    public function calameoPublicationList(): Response
    {
        if (!$this->tableExists(self::TABLE_NAME)) {
            return $this->render('@EzCalameo/admin/calameo/publication_list.html.twig', [
                'items' => [],
                'table_exists' => false,
            ]);
        }

        $items = $this->connection->fetchAllAssociative(
            <<<SQL
            SELECT
                contentobject_attribute_id,
                version,
                publication_id,
                folder_id
            FROM calameo_publication
            ORDER BY contentobject_attribute_id DESC, version DESC
        SQL,
        );

        return $this->render('@EzCalameo/admin/calameo/publication_list.html.twig', [
            'items' => $items,
            'table_exists' => true,
        ]);
    }

    private function countCalameoPublications(): int
    {
        return (int) $this->connection->fetchOne(
            'SELECT COUNT(*) FROM calameo_publication',
        );
    }

    private function tableExists(string $tableName): bool
    {
        try {
            return $this->connection
                ->createSchemaManager()
                ->tablesExist([$tableName]);
        } catch (\Throwable) {
            return false;
        }
    }

    private function getContentTypesWithCalameoPublicationField(): array
    {
        $result = [];

        foreach ($this->contentTypeService->loadContentTypeGroups() as $contentTypeGroup) {
            foreach ($this->contentTypeService->loadContentTypes($contentTypeGroup) as $contentType) {
                $fieldIdentifiers = [];

                foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
                    if ($fieldDefinition->fieldTypeIdentifier !== self::FIELD_TYPE_IDENTIFIER) {
                        continue;
                    }

                    $fieldIdentifiers[] = $fieldDefinition->identifier;
                }

                if ($fieldIdentifiers === []) {
                    continue;
                }

                $result[$contentType->id] = [
                    'id' => $contentType->id,
                    'identifier' => $contentType->identifier,
                    'fields' => $fieldIdentifiers,
                ];
            }
        }

        return $result;
    }

    private function getCalameoPublicationReference(int $fieldId, int $version): ?array
    {
        return $this->connection->fetchAssociative(
            <<<SQL
            SELECT
                publication_id,
                folder_id
            FROM calameo_publication
            WHERE contentobject_attribute_id = :field_id
              AND version = :version
        SQL,
            [
                'field_id' => $fieldId,
                'version' => $version,
            ],
        ) ?: null;
    }

    private function countCalameoFieldDefinitions(): int
    {
        $count = 0;

        foreach ($this->getContentTypesWithCalameoPublicationField() as $contentTypeData) {
            $count += count($contentTypeData['fields']);
        }

        return $count;
    }

    private function countContentsWithCalameoField(): int
    {
        $calameoContentTypes = $this->getContentTypesWithCalameoPublicationField();

        if ($calameoContentTypes === []) {
            return 0;
        }

        $query = new Query([
            'filter' => new Criterion\ContentTypeIdentifier(
                array_column($calameoContentTypes, 'identifier')
            ),
            'limit' => 0,
        ]);

        return $this->searchService->findContent($query)->totalCount;
    }
}
