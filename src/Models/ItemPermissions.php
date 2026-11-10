<?php

declare(strict_types=1);

namespace YouWillLikeIT\Elements\Models;

use SilverStripe\Security\Permission;

/**
 * Delegates item permissions to the owning element (falls back to CMS access).
 */
trait ItemPermissions
{
    public function canView($member = null): bool
    {
        $parent = $this->Parent();

        return $parent && $parent->exists() ? (bool) $parent->canView($member) : true;
    }

    public function canEdit($member = null): bool
    {
        $parent = $this->Parent();

        return $parent && $parent->exists()
            ? (bool) $parent->canEdit($member)
            : Permission::checkMember($member, 'CMS_ACCESS_CMSMain');
    }

    public function canDelete($member = null): bool
    {
        return $this->canEdit($member);
    }

    public function canCreate($member = null, $context = []): bool
    {
        return Permission::checkMember($member, 'CMS_ACCESS_CMSMain');
    }
}
