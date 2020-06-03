<?php


namespace Phobos\Framework\app\Console\Commands\Presets;


class VueSideMenu
{
    /**
     * Create the preset.
     *
     * @return void
     */
    public function create($name)
    {
        $path = base_path().'/frontend/src/store/sideMenu.js';
        $data = file($path);

        $sidemenuDummyTag = '/* { sidemenuDummy } */';

        $data = array_map(function($data) use ($name, $sidemenuDummyTag){
            if (stristr($data, $sidemenuDummyTag)) {
                return "  }, {\n    code: '".$name."s',\n    label: '".ucfirst($name)."s',\n    caption: '".ucfirst($name)."s module',\n    icon: 'fal fa-sun-o',\n    route: '/".$name."s',\n    children: [],\n    link: null,\n    target: null\n  }".$sidemenuDummyTag."]\n";
            }
            return $data;
        },$data);

        file_put_contents($path, implode('', $data));
    }
}
