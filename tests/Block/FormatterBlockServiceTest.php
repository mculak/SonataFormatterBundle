<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\Block;

use Sonata\BlockBundle\Block\BlockContext;
use Sonata\BlockBundle\Model\Block;
use Sonata\BlockBundle\Test\BlockServiceTestCase;
use Sonata\FormatterBundle\Block\FormatterBlockService;
use Symfony\Component\HttpFoundation\Response;

class FormatterBlockServiceTest extends BlockServiceTestCase
{
    public function testExecute(): void
    {
        $block = new Block();

        $blockContext = new BlockContext($block, [
            'format' => 'richhtml',
            'rawContent' => '<b>Insert your custom content here</b>',
            'content' => '<b>Insert your custom content here</b>',
            'template' => '@SonataFormatter/Block/block_formatter.html.twig',
        ]);

        $response = new Response();

        $this->twig->expects(static::once())->method('render')
            ->with('@SonataFormatter/Block/block_formatter.html.twig', [
                'settings' => $blockContext->getSettings(),
                'block' => $blockContext->getBlock(),
            ])
            ->willReturn('TWIG_CONTENT');

        $blockService = new FormatterBlockService($this->twig);
        $blockService->execute($blockContext);
    }

    /**
     * @group legacy
     */
    public function testExecuteWithDeprecatedTeamplating(): void
    {
        if (!class_exists('Sonata\BlockBundle\Block\Service\AbstractAdminBlockService')) {
            static::markTestSkipped('Skipped for block-bundle 4');

            return;
        }

        $block = new Block();

        $blockContext = new BlockContext($block, [
            'format' => 'richhtml',
            'rawContent' => '<b>Insert your custom content here</b>',
            'content' => '<b>Insert your custom content here</b>',
            'template' => '@SonataFormatter/Block/block_formatter.html.twig',
        ]);

        $response = new Response();

        $blockService = new FormatterBlockService('block.service', $this->templating);
        $blockService->execute($blockContext);

        static::assertSame('@SonataFormatter/Block/block_formatter.html.twig', $this->templating->view);

        static::assertIsArray($this->templating->parameters['settings']);
        static::assertInstanceOf(Block::class, $this->templating->parameters['block']);
    }

    public function testDefaultSettings(): void
    {
        $blockService = new FormatterBlockService($this->twig);
        $blockContext = $this->getBlockContext($blockService);

        $this->assertSettings([
            'format' => 'richhtml',
            'rawContent' => '<b>Insert your custom content here</b>',
            'content' => '<b>Insert your custom content here</b>',
            'template' => '@SonataFormatter/Block/block_formatter.html.twig',
        ], $blockContext);
    }
}
