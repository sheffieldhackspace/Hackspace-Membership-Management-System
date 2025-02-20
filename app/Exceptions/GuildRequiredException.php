<?php

namespace App\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;
use RuntimeException;

class GuildRequiredException extends RuntimeException implements ProvidesSolution
{
    /**
     * The exception message.
     *
     * @var string
     */
    protected $message = 'A guild is required if you are going to disable the guild select';

    /**
     * Get the exception solution.
     */
    public function getSolution(): Solution
    {
        return BaseSolution::create('Specify a guild ID before redirecting')
            ->setSolutionDescription('Call `guild($guildId)` before redirecting.');
    }
}
