<?php

namespace Action\Frontend;

abstract class GenericMemberAction extends GenericPublicAction {

    /**
     * @param string $memberId
     * @return \Domain\Member
     */
    protected function getMember(string $memberId): \Domain\Member {
        /** @var $member \Domain\Member */
        $member = $this->em->getRepository(\Domain\Member::class)->find($memberId);
        return $member;
    }
}