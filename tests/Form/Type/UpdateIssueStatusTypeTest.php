<?php

declare(strict_types=1);

namespace App\Tests\Form\Type;

use App\Entity\Issue;
use App\Enum\IssueStatusEnum;
use App\Form\Type\UpdateIssueStatusType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UpdateIssueStatusTypeTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();

        $this->factory = self::getContainer()->get('form.factory');

        parent::setUp();
    }

    public function testUpdateIssueStatus(): void
    {
        $formData = [
            'status' => 'opened',
        ];

        $inputIssueObject = new Issue();

        $form = $this->factory->create(UpdateIssueStatusType::class, $inputIssueObject);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $this->assertEquals(IssueStatusEnum::OPENED, $inputIssueObject->getStatus());
    }
}
