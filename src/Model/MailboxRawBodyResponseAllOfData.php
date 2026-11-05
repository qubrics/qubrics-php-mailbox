<?php

declare(strict_types=1);

// phpcs:disable PSR1.Files.SideEffects

namespace Qubrics\Mailbox\Model;

/*
 * Deprecated name of MailboxRawBody, kept as a class alias until qubrics/mailbox 4.0.
 */
@trigger_error(
    'Qubrics\Mailbox\Model\MailboxRawBodyResponseAllOfData is deprecated; use '
    . 'Qubrics\Mailbox\Model\MailboxRawBody. It will be removed in qubrics/mailbox 4.0.',
    E_USER_DEPRECATED
);
class_alias(MailboxRawBody::class, __NAMESPACE__ . '\MailboxRawBodyResponseAllOfData');

if (false) {
    /**
     * @deprecated use MailboxRawBody
     */
    class MailboxRawBodyResponseAllOfData extends MailboxRawBody
    {
    }
}
