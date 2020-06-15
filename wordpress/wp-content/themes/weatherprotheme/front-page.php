<?php get_header(); ?>

<header>
    <h1><?php the_title(); ?></h1>
    
    <div>

        <?php 
        /* if (have_posts()) : while(have_posts()) : the_post();
            the_content(); 
            endwhile; 
            endif; */ 
        ?>

        <?php 
        $args = array(
            'post_type' => 'sijainnit',

        );

        $the_query = new WP_Query($args);

        if ( $the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                $post_id = get_the_ID();
                
                $coordinates = get_post_meta($post_id, 'sijainti_koordinaatti', true);

                $url = 'https://api.darksky.net/forecast/06b9f4153248185764afc5486d67eece/'. $coordinates .'?units=si';

                $response = file_get_contents($url);
                $response = json_decode($response, true);

                echo $response["currently"]["temperature"]; // ["data"]["currently"]["temperature"]
                echo "<p>" . get_the_title() ."<br>". $coordinates . "</p>";
                
            }
        }
        ?>

    </div>
</header>

<?php get_footer(); ?>