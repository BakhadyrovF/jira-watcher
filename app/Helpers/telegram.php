<?php

/**
 * @param string $text
 * @return string
 */
function toItalic(string $text): string
{
    return "<i>$text</i>";
}

/**
 * @param string $text
 * @return string
 */
function toBold(string $text): string
{
    return "<b>$text</b>";
}

/**
 * @return string
 */
function currentMessage(): string
{
    if (array_key_exists('message', request()->all())) {
        return request()->all()['message']['text'];
    }

    return 'message not found';
}
