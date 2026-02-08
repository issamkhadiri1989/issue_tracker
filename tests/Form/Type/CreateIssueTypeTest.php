<?php

declare(strict_types=1);

namespace App\Tests\Form\Type;

use App\Entity\Category;
use App\Entity\Issue;
use App\Form\Type\CreateIssueType;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

#[AllowMockObjectsWithoutExpectations]
class CreateIssueTypeTest extends KernelTestCase
{
    private FormFactoryInterface $factory;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->factory = self::getContainer()->get('form.factory');

        parent::setUp();
    }

    public function testSubmitValidIssueData(): void
    {
        $formData = [
            'title' => 'test issue title',
            'description' => 'test issue description',
            'category' => 1,
        ];

        $inputIssueObject = new Issue();

        $form = $this->factory->create(CreateIssueType::class, $inputIssueObject);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        // make sure that the object is correctly populated
        $this->assertEquals('test issue title', $inputIssueObject->getTitle());
        $this->assertEquals('test issue description', $inputIssueObject->getDescription());
        $this->assertInstanceOf(Category::class, $inputIssueObject->getCategory());
    }
}
