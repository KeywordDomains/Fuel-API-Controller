Fuel-API-Controller
===================

The API Controller brings RAILS-inspired output-format handling to FuelPHP. It's like a mix of Rest Controller and Template Controller.

Like all Controllers, you create a class in the **fuel/app/class/controller** directory. They need to extend the **Api_Controller** class and are prefixed by default by "**Controller\_**". Below is an example of the controller "test":

	class Controller_Test extends Api_Controller
	{
		// Specify a template for HTML output
		public $template = 'template';
		
		// Notice the REST inspired syntax
		public function get_list()
		{
			$this->handles(array(
				'json' => true, // Json, true that
				'rss' => 'test/feed', // Specify a View File for RSS
				'html' => 'test/list'
			));
			
			// Note that you have to set the template title a tiny bit different
			$this->title('My Test Controller Method');
			
			// And away we go...
			$this->response(array(
				'foo' => 'bar',
				'baz' => array(
					1, 2, 3
				)
			));
		}
	}