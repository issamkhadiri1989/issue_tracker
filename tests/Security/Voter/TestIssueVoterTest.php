<?php

declare(strict_types=1);

namespace App\Tests\Security\Voter;

use App\Entity\Issue;
use App\Entity\Member;
use App\Security\Voter\IssueVoter;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

#[AllowMockObjectsWithoutExpectations]
class TestIssueVoterTest extends KernelTestCase
{
    private object $token;

    protected function setUp(): void
    {
        $this->token = $this->createMock(TokenInterface::class);
    }

    /**
     * @return \Generator used this for better performance
     */
    public static function getUsers(): \Generator
    {
        yield [new Member(), ['CAN_EDIT_ISSUE'], VoterInterface::ACCESS_GRANTED];
        yield [new Member(), ['CAN_DELETE_ISSUE'], VoterInterface::ACCESS_GRANTED];
    }

    public function testVotingWithNoneSupportedAttribute(): void
    {
        self::bootKernel();

        $voter = static::getContainer()->get(IssueVoter::class);

        $issue = new Issue();

        $decision = $voter->vote(token: $this->token, subject: $issue, attributes: ['CAN_VIEW_ISSUE']);

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $decision);
    }

    public function testAnonymousUserTryingToVote(): void
    {
        $this->token
            ->method('getUser')
            ->willReturn(null);

        self::bootKernel();
        $voter = static::getContainer()->get(IssueVoter::class);

        $issue = new Issue();

        $decision = $voter->vote(token: $this->token, subject: $issue, attributes: ['CAN_EDIT_ISSUE']);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $decision);
    }

    #[DataProvider(methodName: 'getUsers')]
    public function testConnectedUserPerformingOperations(Member $member, array $attributes, int $expectedDecision): void
    {
        $this->token
            ->method('getUser')
            ->willReturn($member);

        self::bootKernel();
        $voter = static::getContainer()->get(IssueVoter::class);

        $issue = new Issue()
            ->setReporter($member);

        $decision = $voter->vote(token: $this->token, subject: $issue, attributes: $attributes);

        $this->assertEquals($expectedDecision, $decision);
    }

    public static function getMembersWithRolesSeparately(): \Generator
    {
        yield [new Member(), ['ROLE_ADMIN'], ['CAN_DELETE_ISSUE'], VoterInterface::ACCESS_GRANTED];
        yield [new Member(), ['ROLE_ADMIN'], ['CAN_EDIT_ISSUE'], VoterInterface::ACCESS_GRANTED];
    }

    #[DataProvider(methodName: 'getMembersWithRolesSeparately')]
    public function testAdminPerformingIssueOperations(Member $member, array $withRoles, array $attributes, int $expectedDecision): void
    {
        $this->token
            ->method('getUser')
            ->willReturn($member);

        $mock = $this->createMock(AccessDecisionManagerInterface::class);

        $mock->method('decide')
            ->with($this->token, $withRoles)
            ->willReturn(true);

        $voter = new IssueVoter(accessDecisionManager: $mock);

        $decision = $voter->vote(token: $this->token, subject: new Issue(), attributes: $attributes);
        $this->assertEquals($expectedDecision, $decision);
    }
}
