<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Issue;
use App\Enum\IssueStatusEnum;
use App\Issue\Workflow\TransitionExtractor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UpdateIssueStatusType extends AbstractType
{
    public function __construct(private TransitionExtractor $extractor)
    {
    }

    public function getParent(): ?string
    {
        return CreateIssueType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $data = $options['data'];

        $choices = $this->extractor->extractPossibleTransitions($data);

        $builder->add('status', EnumType::class, [
            'block_prefix' => 'issue_status',
            'choices' => $choices,
            'choice_label' => fn (IssueStatusEnum $item) => $item->humanify(),
            'placeholder' => 'Change issue status',
            'class' => IssueStatusEnum::class,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Issue::class,
            'label' => false,
        ]);
    }
}
