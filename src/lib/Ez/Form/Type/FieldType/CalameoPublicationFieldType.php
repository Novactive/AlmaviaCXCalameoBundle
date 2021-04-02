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
use AlmaviaCX\Calameo\API\Repository\FolderRepository;
use AlmaviaCX\Calameo\Ez\FieldType\CalameoPublication\Value;
use EzSystems\RepositoryForms\Form\Type\FieldType\BinaryBaseFieldType;
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
    /** @var AccountRepository */
    protected $accountRepository;

    /**
     * CalameoPublicationFieldType constructor.
     *
     * @param AccountRepository $accountRepository
     */
    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_calameo_publication';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $folderChoices = [];
        $filteredFolderChoices = [];
        $offset = 0;
        $limit = 20;
        do {
            $availableFolders = $this->accountRepository->fetchAccountFolders($limit, $offset);
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

    public function getParent()
    {
        return BinaryBaseFieldType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'ezrepoforms_fieldtype',
                'available_folder_ids' => [],
            ]
        );
    }
}
