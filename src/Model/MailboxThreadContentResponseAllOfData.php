<?php

declare(strict_types=1);

// phpcs:disable PSR1.Files.SideEffects

namespace Qubrics\Mailbox\Model;

/*
 * Deprecated name of MailboxMessageContent, kept as a class alias until qubrics/mailbox 4.0.
 */
@trigger_error(
    'Qubrics\Mailbox\Model\MailboxThreadContentResponseAllOfData is deprecated; use '
    . 'Qubrics\Mailbox\Model\MailboxMessageContent. It will be removed in qubrics/mailbox 4.0.',
    E_USER_DEPRECATED
);
class_alias(MailboxMessageContent::class, __NAMESPACE__ . '\MailboxThreadContentResponseAllOfData');

if (false) {
    /**
     * @deprecated use MailboxMessageContent
     */
    class MailboxThreadContentResponseAllOfData extends MailboxMessageContent
    {
    }
}
