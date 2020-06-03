<?php


namespace Phobos\Framework\app\Console\Commands\Presets;


class VueRoutes
{
    /**
     * Create the preset.
     *
     * @return void
     */
    public function create($name)
    {
        $path = base_path().'/frontend/src/router/routes.js';
        $data = file($path);

        $routesDummyTag = '/* { routesDummy } */';

        $data = array_map(function($data) use ($name, $routesDummyTag){
            if (stristr($data, $routesDummyTag)) {
                return "\n      { path: '".$name."s', name: '".$name.".listing', component: () => import(/* webpackChunkName: 'app_".$name."s' */ 'pages/".$name."/".ucfirst($name)."ListingPage.vue') },\n      { path: '".$name."s/create', name: '".$name.".create', component: () => import(/* webpackChunkName: 'app_".$name."s' */ 'pages/".$name."/".ucfirst($name)."Page.vue') },\n      { path: '".$name."s/:id', name: '".$name.".edit', component: () => import(/* webpackChunkName: 'app_".$name."s' */ 'pages/".$name."/".ucfirst($name)."Page.vue') },\n      ".$routesDummyTag."\n";
            }
            return $data;
        },$data);

        file_put_contents($path, implode('', $data));
    }
}
