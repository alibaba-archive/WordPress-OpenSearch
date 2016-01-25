# aliyun-opensearch

![Wordpress 4.2](https://img.shields.io/badge/wordpress-4.4.x-blue.svg)
![Wordpress 4.2](https://img.shields.io/badge/wordpress-4.3.x-blue.svg)
![Wordpress 4.2](https://img.shields.io/badge/wordpress-4.2.x-blue.svg)
![Wordpress 4.1](https://img.shields.io/badge/wordpress-4.1.x-blue.svg)
![Wordpress 4.0](https://img.shields.io/badge/wordpress-4.0.x-blue.svg)
![Wordpress 3.9](https://img.shields.io/badge/wordpress-3.9.x-blue.svg)
![Wordpress 3.8](https://img.shields.io/badge/wordpress-3.8.x-blue.svg)
![PHP >= 5.3](https://img.shields.io/badge/php-%3E=5.3-green.svg)

## Introduction
Aliyun Open Search is a hosting service for structured data searching. Supporting data structures, sorting and data processing freedom to customize. Aliyun Open Search provides a simple, low cost, stable and efficient search solution for your sites or applications.

This plugin make an integration of WordPress and Open Search in an easy way.

## Installation

Download the [latest release](http://git.oschina.net/zzqer/WordPress-OpenSearch/repository/archive/master) of this plugin (the file ends with `.zip`).

Open your WordPress admin dashboard: 

1. Open the `plugins` page;
2. Click the `Add New` button;
3. Click the `Upload Plugin` button;
4. Select the zip file you downloaded previous, and click the `Install Now` button;
5. Activate plugin (Just click the link named `activate` after installed success)

Otherwise:

If you can not install this plugin by the method, please firstly download the plugin zip source, and unzip the plugin zip to the {you website root directory}/wp-content/plugins/{your unziped directory}.Lastly, you should active the plugin you just installed handly. Enjoy!

## Configuration 
1. Open your WordPress admin dashboard.
1. Find the menu `Plugins` - `Installed Plugins`, click it, then make sure the plugin named `Open Search` has been activated (Just click the `Activate` link).
2. You can open the options page with `Settings` - `AliYun Open Search Settings` link on left menu.
3. Find the button named `Download Template`,  click it, you will download an file named `config.json`.
4. Fill in the blank named `Access Key ID` with your `Access Key ID` in <a href="https://ak-console.aliyun.com/#/accesskey">`AccessKeys`</a>.
5. Fill in the blank named `Access Key Secret` with your `Access Key Secret` in <a href="https://ak-console.aliyun.com/#/accesskey">`AccessKeys`</a>.
6. Fill in the blank named `Access Host` with your `Access Host` in <a href="http://opensearch.console.aliyun.com/console/#!/apps">`管理控制台>开放搜索>应用管理`</a>.
7. Fill in the blank named `App Name` with your `App Name` in <a href="http://opensearch.console.aliyun.com/console/#!/apps">`管理控制台>开放搜索>应用管理`</a>.

Note that, the default `管理控制台` does not show you `开放搜索`. You should add it by yourself. If you do not know this, please contact with aliyun customer services.

1. Open the Open Search dashboard with link named `阿里云面板`,  then click the link named `模板管理` to create a template for your wordpress search application.
	1. Click the button named `创建模板`;
	1. Type in a `模板名称` such as `wordpress_plugin` , then click the button named `下一步`;
	1. Click the button named `导入模板`, then upload the file named `config.json` that you downloaded in previous steps. 
	2. Click the `下一步` button;
	3. Click the `创建` button on the bottom of page.
	4. It means success after you saw the messages like`创建模板成功`.
	5. Click the link named `返回模板列表`.
2. Click the link named `应用列表` on left menu to create an application.
	1. Click the `创建应用` button;
	2. Make a name for the `应用名称` field, and remember it, it will be useful in next steps.
	3. Select a region, the same region is recommended when your WordPress deployed on AliYun ECS or ACE. It will make the API communication faster.
	4. Click the `下一步` button;
	5. Select the template that you created previous (such as `wordpress_plugin`).
	6. Click the `下一步` button on the bottom of page.
	7. Click the `稍后手动上传` button, then copy the value of `公网API域名` , `内网API域名` is recommended when your WordPress deployed with the same region of  search application.
1. Turn back to your wordpress dashboard and fill in the options form.
2. When everything is ready, you should see a 'upload' button that is just for syncing with your posts to the Open Search server and click it.
3. Have fun!


## See also
http://www.aliyun.com/product/opensearch/
