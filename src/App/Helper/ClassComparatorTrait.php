<?php
namespace Yoanm\JsonRpcParamsSymfonyConstraintDoc\App\Helper;

/**
 * Trait ClassComparatorTrait
 */
trait ClassComparatorTrait
{
    /**
     * @param       $object
     * @param array $classList
     *
     * @return string|null
     */
    protected function getMatchingClassNameIn($object, array $classList) : ?string
    {
        $actualClassList = array_merge(
            [get_class($object)],
            class_implements($object),
            class_uses($object)
        );
        $parentClass = get_parent_class($object);
        while (false !== $parentClass) {
            $actualClassList[] = $parentClass;
            $parentClass = get_parent_class($parentClass);
        }

        $matchList = array_intersect($actualClassList, $classList);

        return array_pop($matchList);
    }
}
