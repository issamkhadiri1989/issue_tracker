<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Category;
use App\Entity\Issue;
use App\Entity\Member;
use App\Enum\SeverityEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateIssueType extends AbstractType
{
    public function __construct(private Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $builder
            ->add('title', Type\TextType::class, [
                'block_prefix' => 'issue_title',
            ])
            ->add('description', Type\TextareaType::class, [
                'block_prefix' => 'issue_description',
                'help' => 'What happened? What were you expecting? Please provide steps to reproduce.',
            ])
            ->add('category', EntityType::class, [
                'block_prefix' => 'issue_category',
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('assignee', EntityType::class, [
                'block_prefix' => 'issue_assignee',
                'placeholder' => 'Unassigned',
                'class' => Member::class,
                'required' => false,
                'choice_label' => function (Member $item) use ($user) {
                    return $item->getFullName().(($item === $user) ? ' (Me)' : '');
                },
            ])
            ->add('severity', Type\EnumType::class, [
                'block_prefix' => 'issue_severity',
                'class' => SeverityEnum::class,
                'expanded' => true,
                'multiple' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_token_id' => '_issue_',
            'csrf_parameter' => '_token',
            'data_class' => Issue::class,
        ]);
    }
}
