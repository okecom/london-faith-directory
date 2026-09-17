<?php

it('can display the public organisation finder', function () {
    $page = visit('/');

    $page->assertNoJavascriptErrors();

     sleep(5);
});

function pauseBrowser(int $seconds = 5): void
{
    sleep($seconds);
}