<?php

namespace Drupal\media_oembed_example\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Returns responses for media_oembed_example routes.
 *
 * @internal
 */
class OEmbedController extends ControllerBase {

  /**
   * Create an oEmbed response.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request for which to create a response.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The oEmbed response.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Error code 501 on invalid `format` parameter, and 404 on invalid
   *   `url` parameter.
   */
  public function media(Request $request) {
    $format = $request->get('format') ?? 'json';
    if ($format !== 'json') {
      throw new HttpException(Response::HTTP_NOT_IMPLEMENTED);
    }

    $url = $request->get('url');
    $matches = [];
    if (!preg_match('%^https://video\.school\.edu/path/(regexp_match_goes_here)%', $url, $matches)) {
      throw new HttpException(Response::HTTP_NOT_FOUND);
    }
    $id = $matches[1];
    $embed = "<div><iframe src=\"https://video.school.edu/path/$id/embed\" allowfullscreen></iframe></div>";

    $maxwidth = $request->get('maxwidth') ?? 480;
    $maxheight = $request->get('maxheight') ?? 360;

    return new JsonResponse([
      'type' => 'video',
      'version' => '1.0',
      'html' => $embed,
      'width' => min(480, $maxwidth),
      'height' => min(360, $maxheight),
    ]);
  }

}
