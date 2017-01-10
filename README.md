# laraforum
<h2>Require</h2>
Forum require packages :
<div class="highlight highlight-source-shell"><pre>https://github.com/cviebrock/eloquent-sluggable</pre></div>
<div class="highlight highlight-source-shell"><pre>https://laravelcollective.com/docs/5.3/html</pre></div>
<h2>Installation</h2>
First, you'll need to install the package via Composer:
<div class="highlight highlight-source-shell"><pre>$ composer require fsflex/laraforum=dev_master</pre></div>
Then, update <code>config/app.php</code>p by adding an entry for the service provider.
<div class="highlight highlight-text-html-php"><pre><span class="pl-s1"><span class="pl-s"><span class="pl-pds">'</span>providers<span class="pl-pds">'</span></span> <span class="pl-k">=&gt;</span> [</span>
<span class="pl-s1">    <span class="pl-c"><span class="pl-c">//</span> ...</span></span>
<span class="pl-s1">    <span class="pl-c1">FsFlex\LaraForum\</span><span class="pl-c1">LaraForumServiceProvider</span><span class="pl-k">::</span><span class="pl-c1">class</span>,</span>
<span class="pl-s1">];</span></pre></div>
Finally, from the command line again, publish the default configuration file:
<div class="highlight highlight-source-shell"><pre>php artisan vendor:publish --provider=<span class="pl-s"><span class="pl-pds">"</span>FsFlex\LaraForum\LaraForumServiceProvider<span class="pl-pds">"</span></span></pre></div>
<h2> Configuration </h2>
By default, global configuration can be set in the <code>config/laraforum.php</code> file. 
If a configuration isn't set, then the package defaults from <code>vendor/fsflex/laraforum/src/resources/config/laraforum.php</code> are used.
Here is an example configuration, with all the default settings shown:
<div class="highlight highlight-text-html-php"><pre>return [
    'template' => 'discuss',
    'pagename' => 'LaraForum',
    'basic_title' => 'title forum',
    'basic_description'=>'forum description',
    'url_prefix' => 'laraforum',
    'admin_username' => 'admin',
    'posts_interval' => 30,//seconds
    'threads_interval' => 300,//seconds
    'threads_paginate' => 20,
    'posts_paginate' =>20,
];</pre></div>

