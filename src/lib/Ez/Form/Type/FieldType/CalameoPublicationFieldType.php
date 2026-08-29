<?php

/*
 * AlmaviaCXCalameoBundle Bundle.
 *
 * @author    AlmaviaCX
 * @copyright 2021 AlmaviaCX
 * @license   MIT Licence
 */

declare(strict_types=1);

namespace AlmaviaCX\Calameo\Ez\Form\Type\FieldType;

use AlmaviaCX\Calameo\API\Repository\AccountRepository;
use AlmaviaCX\Calameo\Exception\ApiResponseErrorException;
use AlmaviaCX\Calameo\Ez\FieldType\CalameoPublication\Value;
use Ibexa\ContentForms\Form\Type\FieldType\BinaryBaseFieldType;
use Ibexa\Contracts\AdminUi\Notification\NotificationHandlerInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalameoPublicationFieldType extends AbstractType
{
    public function __construct(
        protected readonly AccountRepository $accountRepository,
        protected readonly NotificationHandlerInterface $notificationHandler
    )
    {
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix(): string
    {
        return 'ezplatform_fieldtype_calameo_publication';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $folderChoices = [];
        $filteredFolderChoices = [];
        $offset = 0;
        $limit = 20;
        do {
            try {
                $availableFolders = $this->accountRepository->fetchAccountFolders($limit, $offset);
            } catch (\GuzzleHttp\Exception\ClientException|ApiResponseErrorException $exception) {
                $this->notificationHandler->error(
                    sprintf("[Calameo] %s", $exception->getMessage())
                );
                break;
            }
            foreach ($availableFolders->items as $availableFolder) {
                if (
                    empty($options['available_folder_ids']) ||
                    in_array($availableFolder->id, $options['available_folder_ids'])
                ) {
                    $filteredFolderChoices[$availableFolder->name] = $availableFolder->id;
                }
                $folderChoices[$availableFolder->name] = $availableFolder->id;
            }

            $offset += $limit;
        } while ($availableFolders->total > $offset);


        $builder
            ->add(
                'publicationId',
                HiddenType::class
            )
            ->add(
                'folderId',
                ChoiceType::class,
                [
                    'choices' => $filteredFolderChoices,
                ]
            )
            ->add(
                'remove',
                CheckboxType::class,
                [
                    'label' => /** @Desc("Remove") */ 'content.field_type.binary_base.remove',
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label'    => /** @Desc("File") */ 'content.field_type.binary_base.file',
                    'required' => $options['required'],
                ]
            );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            static function (FormEvent $event) use ($folderChoices) {
                /** @var Value $data */
                $data = $event->getData();
                $form = $event->getForm();

                if ($data->publicationId !== null) {
                    $form->add(
                        'folderId',
                        ChoiceType::class,
                        [
                            'choices'  => $folderChoices,
                            'disabled' => true,
                        ]
                    );
                }
            }
        );
    }

    public function getParent(): string
    {
        return BinaryBaseFieldType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'ezrepoforms_fieldtype',
                'available_folder_ids' => [],
            ]
        );
    }
}
