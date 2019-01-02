<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AnnonceType extends AbstractType
{

    /**
     * Permet deè
     *
     * @param [type] $label
     * @param [type] $placeholder
     * @return void
     */
    private function getConfiguration($label, $placeholder){
        return [
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder,
            ],
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                 TextType::class,
                  $this->getConfiguration('Titre', 'Tapez votre titre'))
            ->add(
                'coverImage',
                 UrlType::class,
                  $this->getConfiguration("Url de l'image", "Importez l'image"))
            ->add(
                'introduction',
                 TextType::class,
                  $this->getConfiguration("Introduction", "Donnez une desciption global de l'annonce"))
            ->add(
                'content',
                 TextareaType::class,
                  $this->getConfiguration("Contenu", "Description détaillée"))
            ->add(
                'rooms',
                 IntegerType::class,
                  $this->getConfiguration("Nombre de chambre(s)", "Saisisez le nombre de chambre(s)"))
            ->add(
                'price',
                 MoneyType::class,
                  $this->getConfiguration('Prix par nuits', 'Tapez votre prix'))
            ->add(
                'images',
                CollectionType::class, [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
