<?php

namespace App\Overwritten;

/**
 * Overwrite the default Gate class to be able to resolve anonymous classes of policies
 */
class Gate extends \Illuminate\Auth\Access\Gate
{
    public function getPolicyFor($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (! is_string($class)) {
            return;
        }

        if (isset($this->policies[$class])) {
            if (is_string($this->policies[$class])) {
                return $this->resolvePolicy($this->policies[$class]);
            } else {
                return $this->policies[$class];
            }
        }

        foreach ($this->guessPolicyName($class) as $guessedPolicy) {
            if (class_exists($guessedPolicy)) {
                return $this->resolvePolicy($guessedPolicy);
            }
        }

        foreach ($this->policies as $expected => $policy) {
            if (is_subclass_of($class, $expected)) {
                return $this->resolvePolicy($policy);
            }
        }
    }
}
