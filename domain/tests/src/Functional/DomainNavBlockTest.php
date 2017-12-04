<?php

namespace Drupal\Tests\domain\Functional;

use Drupal\Core\Session\AccountInterface;
use Drupal\Tests\domain\Functional\DomainTestBase;

/**
 * Tests the domain navigation block.
 *
 * @group domain
 */
class DomainNavBlockTest extends DomainTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('domain', 'node', 'block');

  /**
   * Test domain navigation block.
   */
  public function testDomainNav() {
    // Create four new domains programmatically.
    $this->domainCreateTestDomains(4);
    $domains = \Drupal::service('entity_type.manager')->getStorage('domain')->loadMultiple();

    // Place the nav block.
    $this->drupalPlaceBlock('domain_nav_block');

    // Let the anon user view the block.
    user_role_grant_permissions(AccountInterface::ANONYMOUS_ROLE, array('use domain nav block'));

    // Load the homepage. All links should appear.
    $this->drupalGet('<front>');
    // Confirm domain links.
    foreach ($domains as $id => $domain) {
      $this->findLink($domain->label());
    }

    // Disable one of the domains. One link should not appear.
    $disabled = $domains['one_example_com'];
    $disabled->disable();

    // Load the homepage.
    $this->drupalGet('<front>');
    // Confirm domain links.
    foreach ($domains as $id => $domain) {
      if ($id != 'one_example_com') {
        $this->findLink($domain->label());
      }
      else {
        $this->assertNoRaw($domain->label());
      }
    }
    // Let the anon user view diabled domains. All links should appear.
    user_role_grant_permissions(AccountInterface::ANONYMOUS_ROLE, array('access inactive domains'));

    // Load the homepage.
    $this->drupalGet('<front>');
    // Confirm domain links.
    foreach ($domains as $id => $domain) {
      $this->findLink($domain->label());
    }
  }
}
