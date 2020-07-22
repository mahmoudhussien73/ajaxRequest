
<?php


function mainSearchResults($data) {
  $mainQuery = new WP_Query(array(
    'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
    's'         => sanitize_text_field($data['term'])
  ));

  $results = array(
    'generalInfo' => array(),
    'professors'  => array(),
    'programs'    => array(),
    'campuses'    => array(),
    'events'      => array()
  );

  while($mainQuery->have_posts()){ $mainQuery->the_post();
    if(get_post_type() == 'post' OR get_post_type() == 'page'){
        array_push($results['generalInfo'], array( //start array push
          'title'         => get_the_title(),
          'postType'      => get_post_type(),
          'authorName'    => get_the_author(),
          'permaLink'     => get_the_permalink()
        ));//end array push
    }

    if(get_post_type() == 'professor'){
        array_push($results['professors'], array( //start array push
          'title'         => get_the_title(),
          'permaLink'     => get_the_permalink(),
          'image'         => get_the_post_thumbnail_url(0, 'prpfessorLandScape')
        ));//end array push
    }

    if(get_post_type() == 'program'){

        $relatedCampuses = get_field('related_campus');
        if($relatedCampuses){
          foreach($relatedCampuses as $campuse){
            array_push($results['campuses'], array(
              'title' => get_the_title($campuse),
              'permaLink' => get_the_permalink($campuse)
            ));
          }
        }

        array_push($results['programs'], array( //start array push
          'title'         => get_the_title(),
          'permaLink'     => get_the_permalink(),
          'id'            => get_the_id()
        ));//end array push
    }

    if(get_post_type() == 'campus'){
        array_push($results['campuses'], array( //start array push
          'title'         => get_the_title(),
          'permaLink'     => get_the_permalink()
        ));//end array push
    }

    if(get_post_type() == 'event'){
      $eventDate = new DateTime(get_field('event_date'));
      $description = null;
      if (has_excerpt()) {
             $description = get_the_excerpt();
            } else {
             $description = wp_trim_words(get_the_content(), 18);
              }
        array_push($results['events'], array( //start array push
          'title'         => get_the_title(),
          'permaLink'     => get_the_permalink(),
          'day'           => $eventDate->format('d'),
          'month'         => $eventDate->format('M'),
          'description'   => $description
        ));//end array push
    }
  }

  wp_reset_postdata(); // end first while and finish custom query

  if($results['programs']){
    $programsMetaQuery = array('relation' => 'OR');
    foreach($results['programs'] as $item){
      array_push($programsMetaQuery, array(
        'key' => 'related_programs',
        'compare' => 'LIKE',
        'value'   => '"'.$item['id'].'"'
      ));
    }

    $relatedProgramesAndProfessors = new WP_Query(array(
      'post_type'   => array('professor', 'event'),
      'meta_query'  => $programsMetaQuery
    ));

    while( $relatedProgramesAndProfessors->have_posts()){ $relatedProgramesAndProfessors->the_post();

       if(get_post_type() == 'professor'){
            array_push($results['professors'], array( //start array push
              'title'         => get_the_title(),
              'permaLink'     => get_the_permalink(),
              'image'         => get_the_post_thumbnail_url(0, 'prpfessorLandScape')
            ));//end array push
        }

      if(get_post_type() == 'event'){
        $eventDate = new DateTime(get_field('event_date'));
        $description = null;
        if (has_excerpt()) {
               $description = get_the_excerpt();
              } else {
               $description = wp_trim_words(get_the_content(), 18);
                }
          array_push($results['events'], array( //start array push
            'title'         => get_the_title(),
            'permaLink'     => get_the_permalink(),
            'day'           => $eventDate->format('d'),
            'month'         => $eventDate->format('M'),
            'description'   => $description
          ));//end array push
      }

    }
    $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
    $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
  }
  return $results;
}