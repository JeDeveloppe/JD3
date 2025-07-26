<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class NinjasApiService
{
    public function __construct(
        private HttpClientInterface $client
        ){
    }

    public function getDatasFromOnePlanet(string $planet){

        // //? GET request to unsplash
        $response = $this->client->request(
            'GET',
            'https://api.api-ninjas.com/v1/planets?name='.$planet,
            [
            'headers' => [
                'X-Api-Key' => $_ENV['NINJAS_API_ACCESS_KEY']
            ]
        ]);

        $donnees = $response->toArray();
        
        //?exemple of donnees
        /*
        array:30 [▼
            "id" => "LuiP3ojMg5w"
            "slug" => "a-view-of-a-waterfall-from-a-high-point-of-view-LuiP3ojMg5w"
            "alternative_slugs" => array:8 [▶]
            "created_at" => "2024-11-03T19:07:12Z"
            "updated_at" => "2024-12-21T23:42:57Z"
            "promoted_at" => "2024-12-31T00:17:00Z"
            "width" => 5088
            "height" => 3392
            "color" => "#d9f3f3"
            "blur_hash" => "LhFib_IWaexZ?dt4j[af?vjZRlR*"
            "description" => null
            "alt_description" => "A view of a waterfall from a high point of view"
            "breadcrumbs" => []
            "urls" => array:6 [▼
                "raw" => "https://..."
                "full" => "https://..."
                "regular" => "https://..."
                "small" => "https://..."
                "thumb" => "https://..."
                "small_s3" => "https://s3.us-west-2.amazonaws.com/images.unsplash.com/small/photo-1730660666237-1e6a008067a9"
            ]
            "links" => array:4 [▶]
            "likes" => 38
            "liked_by_user" => false
            "current_user_collections" => []
            "sponsorship" => null
            "topic_submissions" => array:2 [▶]
            "asset_type" => "photo"
            "user" => array:22 [▶]
            "exif" => array:7 [▶]
            "location" => array:4 [▶]
            "meta" => array:1 [▶]
            "public_domain" => false
            "tags" => array:19 [▶]
            "views" => 99942
            "downloads" => 1799
            "topics" => array:2 [▶]
            ]
            */

        return $donnees;
    }

}