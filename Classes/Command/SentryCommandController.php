<?php
declare(strict_types=1);

namespace Flownative\Sentry\Command;

/*
 * This file is part of the Flownative.Sentry package.
 *
 * (c) Robert Lemke, Flownative GmbH - www.flownative.com
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Flownative\Sentry\SentryClient;
use Neos\Flow\Annotations\Inject;
use Neos\Flow\Cli\CommandController;
use RuntimeException;
use Sentry\Severity;

final class SentryCommandController extends CommandController
{
    /**
     * @Inject
     * @var SentryClient
     */
    protected $sentryClient;

    /**
     * Test the setup
     *
     * This command allows you to test the Sentry integration and validates that the
     * configuration, credentials and connection to the Sentry server work fine.
     *
     * For testing purposes, an event will be sent to Sentry.
     */
    public function testCommand(): void
    {
        $this->output->outputLine('<b>Testing Sentry setup …</b>');
        $this->output->outputLine('Using the following configuration:');

        $options = $this->sentryClient->getOptions();

        $this->output->outputTable([
            ['DSN', $options->getDsn()],
            ['Environment', $options->getEnvironment()],
            ['Release', $options->getRelease()],
            ['Server Name', $options->getServerName()],
            ['Sample Rate', $options->getSampleRate()]
        ], [
            'Option',
            'Value'
        ]);

        $eventId = $this->sentryClient->captureMessage(
            'Flownative Sentry Plugin Test',
            Severity::debug(),
            [
                'Flownative Sentry Client Version' => 'dev'
            ]
        );

        $this->outputLine('<success>An informational message was sent to Sentry</success> Event ID: #%s', [$eventId]);
        $this->outputLine();
        $this->outputLine('This command will now throw an exception for testing purposes.');
        $this->outputLine();

        $this->throwException('Some argument');
    }

    /**
     * @param string $someArgument
     */
    protected function throwException(string $someArgument): void
    {
        $previousException = new \InvalidArgumentException('Test "previous" exception thrown by the SentryCommandController', 1614759554);
        throw new RuntimeException('Test exception in SentryCommandController', 1614759519, $previousException);
    }
}
