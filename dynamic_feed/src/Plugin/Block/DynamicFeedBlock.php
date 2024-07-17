namespace Drupal\dynamic_feed\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides a 'Dynamic Feed Block' Block.
 *
 * @Block(
 *   id = "dynamic_feed_block",
 *   admin_label = @Translation("Dynamic Feed Block"),
 * )
 */
class DynamicFeedBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The HTTP client to fetch the API data.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * Constructs a new DynamicFeedBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \GuzzleHttp\Client $http_client
   *   The Guzzle HTTP client.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Client $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get the post ID from the URL query parameters.
    $post_id = \Drupal::request()->query->get('post_id');
    if ($post_id) {
      try {
        // Fetch data from the API.
        $response = $this->httpClient->request('GET', "https://jsonplaceholder.typicode.com/posts/{$post_id}");
        $data = json_decode($response->getBody(), TRUE);

        // Build the render array.
        if ($data) {
          return [
            '#theme' => 'dynamic_feed',
            '#title' => $data['title'],
            '#body' => $data['body'],
            '#cache' => [
              'max-age' => 0,
            ],
          ];
        }
        else {
          return [
            '#markup' => $this->t('No data found for post ID @id.', ['@id' => $post_id]),
            '#cache' => [
              'max-age' => 0,
            ],
          ];
        }
      }
      catch (\Exception $e) {
        return [
          '#markup' => $this->t('Unable to fetch data.'),
          '#cache' => [
            'max-age' => 0,
          ],
        ];
      }
    }
    else {
      return [
        '#markup' => $this->t('No post ID provided.'),
        '#cache' => [
          'max-age' => 0,
        ],
      ];
    }
  }
}
