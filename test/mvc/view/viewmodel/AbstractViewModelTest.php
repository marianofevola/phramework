<?php


use Assertify\Framework\TestCase;
use Phramework\Mvc\View\ViewModel\AbstractViewModel;

class AbstractViewModelTest extends TestCase
{

  public function testSetBreadcrumbsByUri()
  {
    $viewModel = new AbstractViewModel();

    $viewModel->setBreadcrumbsByUri("/profile/change-password");

    $breadcrumbs = $viewModel->getBreadcrumbs()->toArray();

    $expectedBreadcrumbs = [
      "/" => "Home",
      "/profile" => "Profile",
      "/change-password" => "Change Password"
    ];

    $this->assertEquals(
      $expectedBreadcrumbs,
      $breadcrumbs
    );

  }

  public function testSetBreadcrumbsByUriHome()
  {
    $viewModel = new AbstractViewModel();
    $viewModel->setBreadcrumbsByUri("/");
    $this->assertFalse($viewModel->hasBreadCrumbs());
  }

}
