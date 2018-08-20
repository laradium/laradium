<?php

namespace Netcore\Aven\Http\Controllers\Admin;

use App\Models\ContentBlock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{

    public function contentBlockDelete($id)
    {
        $contentBlock = ContentBlock::find($id);

        $blockClass = $contentBlock->block_type;
        $blockId = $contentBlock->block_id;
        $block = new $blockClass;
        $block->find($blockId)->delete();

        $contentBlock->delete();

        return [
            'state' => 'success'
        ];
    }
}
