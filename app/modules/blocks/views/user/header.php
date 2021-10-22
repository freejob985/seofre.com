    <header class="header fixed-top" id="headerNav">
      <div class="container">
        <nav class="navbar navbar-expand-lg ">
          <a class="navbar-brand" href="<?php echo cn(); ?>">
            <img class="site-logo" src="<?php echo get_option('website_logo', BASE."assets/images/logo.png"); ?>" alt="Webstie logo">
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span><i class="fe fe-menu"></i></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
              <li class="nav-item <?php echo (segment(1) == '') ? "active" : ""?>">
                <a class="nav-link js-scroll-trigger" href="<?php echo cn(); ?>"><?php echo lang("Home"); ?></a>
              </li>

              <li class="nav-item dropdown <?php echo (!empty(segment(1)) && !in_array(segment(1), ['blog', 'faq', 'contact', 'terms']) ) ? "active" : ""?>">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <?php echo lang('Services'); ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                  <?php 
                    if (!empty($all_items)) {
                      foreach ($all_items as $key => $social_network) {
                  ?>
                  <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#"><?php echo strip_tags($key); ?></a>
                    <ul class="dropdown-menu">
                      <?php
                        $categories = $social_network;
                        foreach ($categories as $key => $category) {
                      ?>
                      <li class="dropdown-submenu"><a class="dropdown-item" href="<?php echo cn($category->url_slug); ?>"><?php echo lang('Buy'); ?> <?php echo strip_tags($category->name); ?></a>

                      <?php } ?>
                    </ul>
                  </li>
                  <?php }}?>
                </ul>
              </li>
              <li class="nav-item <?php echo (segment(1) == 'blog') ? "active" : ""?>">
                <a class="nav-link js-scroll-trigger" href="<?php echo cn('blog'); ?>"><?php echo lang('Blog'); ?></a>
              </li>              

              <li class="nav-item <?php echo (segment(1) == 'faq') ? "active": ""?>">
                <a class="nav-link js-scroll-trigger" href="<?php echo cn('faq'); ?>"><?php echo lang('FAQ'); ?></a>
              </li>

              <li class="nav-item <?php echo (segment(1) == 'contact') ? "active" : ""?>">
                <a class="nav-link js-scroll-trigger" href="<?php echo cn('contact'); ?>"><?php echo lang('Contact'); ?></a>
              </li>

            </ul>
          </div>
        </nav>
      </div>
    </header>