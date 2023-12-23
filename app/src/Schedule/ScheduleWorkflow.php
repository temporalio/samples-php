<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Schedule;

use Temporal\Workflow;

class ScheduleWorkflow implements ScheduleWorkflowInterface
{
    public const PHRASES = [
        'en' => ['Hello, %s!', 'Hi, %s!', 'Hey, %s!', 'Greetings, %s!', 'How are you, %s?'],
        'ru' => ['Привет, %s!', 'Здравствуйте, %s!', 'Здорово, %s!', 'Приветствую, %s!', 'Как дела, %s?'],
        'ua' => ['Привіт, %s!', 'Здрастуйте, %s!', 'Здорово, %s!', 'Привітання, %s!', 'Як справи, %s?'],
        'fr' => ['Bonjour, %s!', 'Salut, %s!', 'Salutations, %s!', 'Comment allez-vous, %s?', 'Comment vas-tu, %s?'],
        'de' => ['Hallo, %s!', 'Hi, %s!', 'Grüße, %s!', 'Wie geht es dir, %s?', 'Wie geht es Ihnen, %s?'],
    ];
    public const FALLBACK_LANGUAGE = 'en';

    public function greet(string $name): string
    {
        \error_log('Scheduled workflow has been executed.');

        $language = Workflow::getCurrentContext()->getHeader()->getValue('language') ?? self::FALLBACK_LANGUAGE;
        $language = \array_key_exists($language, self::PHRASES) ? $language : self::FALLBACK_LANGUAGE;

        return \sprintf(self::PHRASES[$language][\array_rand(self::PHRASES[$language])], $name);
    }
}
