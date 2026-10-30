<?php

use CodeIgniter\Test\CIUnitTestCase;

final class SupportChatResizeAffordanceTest extends CIUnitTestCase
{
    public function testChatUsesAnInvisibleCornerGripInsteadOfSizeButtons(): void
    {
        $template = file_get_contents(APPPATH . 'Views/layout/template.php');
        $script = file_get_contents(FCPATH . 'js/ilena-support-chat.js');
        $styles = file_get_contents(FCPATH . 'css/ilena-storefront.css');

        $this->assertStringContainsString('<div class="ilena-chat-resize-handle" data-chat-resize', $template);
        $this->assertStringNotContainsString('data-chat-size=', $template);
        $this->assertStringNotContainsString('ilena-chat-size-controls', $template . $styles);
        $this->assertStringNotContainsString('sizeButtons', $script);
        $this->assertStringContainsString('cursor: nwse-resize', $styles);
        $this->assertStringContainsString("resizeHandle?.addEventListener('pointerdown'", $script);
        $this->assertStringContainsString("resizeHandle?.addEventListener('pointermove'", $script);
    }
}
