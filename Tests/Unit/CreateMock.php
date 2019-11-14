<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit;

trait CreateMock
{
    /**
     * Create mock
     * @param $originalClassName
     * @return mixed
     */
    public function createMockTrait($originalClassName)
    {
        return $this->getMockBuilder($originalClassName)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();
    }

    /**
     * Returns a partial mock object for the specified class.
     *
     * @param string|string[] $originalClassName
     * @param string[]        $methods
     *
     * @psalm-template RealInstanceType of object
     * @psalm-param class-string<RealInstanceType>|string[] $originalClassName
     * @psalm-return MockObject&RealInstanceType
     */
    protected function createPartialMock($originalClassName, array $methods)
    {
        $class_names = \is_array($originalClassName) ? $originalClassName : [$originalClassName];
        foreach ($class_names as $class_name) {
            $reflection = new \ReflectionClass($class_name);
            $mockedMethodsThatDontExist = \array_filter(
                $methods,
                static function ($method) use ($reflection) {
                    return !$reflection->hasMethod($method);
                }
            );
            if ($mockedMethodsThatDontExist) {
                $this->addWarning(
                    \sprintf(
                        'createPartialMock called with method(s) %s that do not exist in %s. This will not be allowed in future versions of PHPUnit.',
                        \implode(', ', $mockedMethodsThatDontExist),
                        $class_name
                    )
                );
            }
        }
        return $this->getMockBuilder($originalClassName)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->setMethods(empty($methods) ? null : $methods)
            ->getMock();
    }
}
