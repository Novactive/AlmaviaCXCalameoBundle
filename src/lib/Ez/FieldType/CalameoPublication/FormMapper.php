<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\Ez\FieldType\CalameoPublication;

use AlmaviaCX\Calameo\API\Repository\AccountRepository;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use AlmaviaCX\Calameo\Ez\Form\Type\FieldType\CalameoPublicationFieldType;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\Core\FieldType\BinaryFile\Value;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\DataTransformer\BinaryFileValueTransformer;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use EzSystems\RepositoryForms\Form\Type\FieldType\BinaryFileFieldType;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormMapper implements FieldValueFormMapperInterface, FieldDefinitionFormMapperInterface
{
    /** @var FieldTypeService */
    protected $fieldTypeService;

    /** @var AccountRepository */
    protected $accountRepository;

    /** @var NotificationHandlerInterface */
    protected $notificationHandler;

    /**
     * @param FieldTypeService             $fieldTypeService
     * @param AccountRepository            $accountRepository
     * @param NotificationHandlerInterface $notificationHandler
     */
    public function __construct(
        FieldTypeService $fieldTypeService,
        AccountRepository $accountRepository,
        NotificationHandlerInterface $notificationHandler
    ) {
        $this->fieldTypeService = $fieldTypeService;
        $this->accountRepository = $accountRepository;
        $this->notificationHandler = $notificationHandler;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $folderChoices = [];
        $offset = 0;
        $limit = 20;
        do {
            try {
                $availableFolders = $this->accountRepository->fetchAccountFolders($limit, $offset);
            } catch (ApiResponseErrorException $exception) {
                $this->notificationHandler->error(
                    sprintf("[Calameo] %s", $exception->getMessage())
                );
                break;
            }
            foreach ($availableFolders->items as $availableFolder) {
                $folderChoices[$availableFolder->name] = $availableFolder->id;
            }

            $offset += $limit;
        } while ($availableFolders->total > $offset);

        $fieldDefinitionForm
            ->add(
                'availableFolderIds',
                ChoiceType::class,
                [
                    'choices'       => $folderChoices,
                    'multiple'      => true,
                    'property_path' => 'fieldSettings[availableFolderIds]',
                    'label'         => 'field_definition.calameo_publication.availableFolderIds',
                    'required'      => false
                ]
            );
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $fieldType = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        CalameoPublicationFieldType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label'    => $fieldDefinition->getName(),
                            'available_folder_ids' => $fieldDefinition->fieldSettings['availableFolderIds'] ?? []
                        ]
                    )
                    ->addModelTransformer(
                        new ValueTransformer(
                            $fieldType,
                            $data->value
                        )
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'translation_domain' => 'ezrepoforms_content_type',
                ]
            );
    }
}
