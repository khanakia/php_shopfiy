<?php
namespace Khanakia\Shopify;

use Goutte\Client;

class ReviewExtractor {

  public $reviews = [];
  
  public function __construct($appName='')
  {
    $this->appName = $appName;
      $this->client = new Client();
  }

  public function getAppUrl() {
    return "https://apps.shopify.com/{$this->appName}";
  }

  public function getReviewPageUrl($page=0) {
    $url = $this->getAppUrl()."/reviews";
    if($page > 1) {
      $url .= '?page=' . $page;
    }
    return $url;
  }

  public function extractReviews($crawler) {
    // var_dump($client->getResponse()->getContent());
    $reviews = $crawler->filter('div.review-listing');
  
    // $reviews = $reviews->first();
    $reviews->each(function ($node, $i) use (&$data) {
      $item = [];
      $item['title'] = $node->filter('.review-listing-header__text')->text();
      $item['rating'] = $node->filter('.ui-star-rating')->attr('data-rating');
      $item['date'] = $node->filter('.review-metadata__item-label')->text();
      $item['content'] = $node->filter('.review-content .truncate-content-copy p')->text();
    
      $item['location'] = $node->filter('.review-merchant-characteristic__item span')->text();
      $this->reviews[] = $item;
    });
  }

  public function do($page=0) {
    $crawler = $this->client->request('GET', $this->getReviewPageUrl($page));
    $this->extractReviews($crawler);

    // it means we want to extract single page
    if($page > 0) {
      return;
    }

    $nextAnchor = $crawler->filter('.search-pagination__next-page-text');

    if($nextAnchor->count() > 0) {
      $nextUrl = "https://apps.shopify.com".$nextAnchor->attr('href');
      // echo "NEXT PAGE". $nextUrl;
      $crawler = $this->client->request('GET', $nextUrl);
      $this->extractReviews($crawler);
    }
  }

  public function prettyPrint() {
    $json_pretty = json_encode($this->reviews, JSON_PRETTY_PRINT);
    echo ($json_pretty)."\n";
  }
}