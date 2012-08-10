<?php

/**
 *	API Controller class
 *
 *	A base controller that brings Rails-inspired output handling to FUEL
 *
 *	@package  Fuel
 *	@category Controller
 *	@author   Lucas Nolte <lnolte@i.biz>
 */

class Api_Controller extends Controller_Rest
{
	/**
	 * @var array handles
	 */
	protected $_handles = array();
	
	/**
	 * @var string page template
	 */
	public $template = 'template';
	
	/**
	 * @var string page title
	 */
	public $title = '';

	/**
	 *	Add support for new formats here
	 */
	public function before()
	{
		$this->_supported_formats['rss'] = 'application/rss+xml';
		parent::before();
	}

	/**
	 * After the controller method has run output the template
	 *
	 * @param Response $response
	 */
	public function after($response)
	{
		if(	$this->format == 'html'	)
		{
			// If nothing was returned default to the template
			if( empty($response) )
			{
				$response = $this->template;
			}
			
			// If the response isn't a Repsonse object, embed in the available one for BC
			if( ! $response instanceof \Response)
			{
				$this->response->body = $response;
				$response = $this->response;
			}
		}

		return parent::after($response);
	}

	/**
	 * Add supported formats to the controller
	 *
	 * @param array $handles
	 */
	public function handles($handles)
	{
		$this->_handles = $handles;
	}

	/**
	 * 
	 *
	 * @param string $title
	 */
	public function title($title)
	{
		$this->title = $title;
	}

	/**
	 * Response
	 *
	 * Basically an alias of Controller_Rest::response
	 *
	 * @param mixed
	 * @param int
	 */
	protected function response($data = array(), $http_code = 200)
	{
		if ( isset( $this->_handles[$this->format] ) )
		{
			// Set the correct format header
			$this->response->set_header('Content-Type', $this->_supported_formats[$this->format]);

			// Check whether output format is just supported...
			if( $this->_handles[$this->format] === true )
			{
				$this->response->body(\Format::forge($data)->{'to_'.$this->format}());
			}

			// ..or whether it has a view file attached
			else
			{
				// If format is html, embed stuff in the global template
				if( $this->format == 'html' )
				{
					$this->template = \View::forge($this->template);

					$this->template->title = $this->title;
					$this->template->content = \View::forge($this->_handles[$this->format], $data);
				}

				// If it's not HTML, just build the view and go away
				else 
				{
					$this->response->body(\View::forge($this->_handles[$this->format], $data));
				}
			}
		}
		
		else
		{
			// If the requested format is not supported, throw a 404 error
			throw new HttpNotFoundException;
		}
	}
}