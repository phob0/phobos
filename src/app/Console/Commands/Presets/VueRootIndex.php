<?php


namespace Phobos\Framework\app\Console\Commands\Presets;


class VueRootIndex
{
    /**
     * Create the preset.
     *
     * @return void
     */
    public function create($name)
    {
        $path = base_path().'/frontend/src/store/index.js';
        $data = file($path);

        $importDummyTag = '/* { importDummy } */';
        $modulesDummyTag = '/* { modulesDummy } */';
        $moduleDummyTag = '/* { moduleDummy } */';

        $data = array_map(function($data) use ($name, $importDummyTag){
            if (stristr($data, $importDummyTag)) {
                return "import ".$name." from './".$name."'\n".$importDummyTag."\n";
            }
            return $data;
        },$data);

        $data = array_map(function($data) use ($name, $modulesDummyTag){
            if (stristr($data, $modulesDummyTag)) {
                return "      ".$name.",\n".$modulesDummyTag."\n";
            }
            return $data;
        },$data);

        $data = array_map(function($data) use ($name, $moduleDummyTag){
            if (stristr($data, $moduleDummyTag)) {
                return "    module.hot.accept('./".$name."', () => {\n      const newModule = require('./".$name."').default\n      Store.hotUpdate({ modules: { ".$name.": newModule } })\n    })\n\n".$moduleDummyTag."\n";
            }
            return $data;
        },$data);

        file_put_contents($path, implode('', $data));
    }
}
