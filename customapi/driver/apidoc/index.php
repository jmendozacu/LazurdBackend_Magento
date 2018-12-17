<?php
  $project_name = 'lazurd.com - Driver App APi';
  $api_path = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME']."/customapi/driver";
?>

<!DOCTYPE html>
<html class=" js flexbox canvas canvastext webgl no-touch geolocation postmessage websqldatabase indexeddb hashchange history draganddrop websockets rgba hsla multiplebgs backgroundsize borderimage borderradius boxshadow textshadow opacity cssanimations csscolumns cssgradients cssreflections csstransforms csstransforms3d csstransitions fontface generatedcontent video audio localstorage sessionstorage webworkers applicationcache svg inlinesvg smil svgclippaths" lang="en"><!--<![endif]--><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title><?=$project_name?> - API Documentation</title>
  <link href="./includes/css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="./includes/sphinx_rtd_theme.css" type="text/css">
  <link rel="stylesheet" href="./includes/readthedocs-doc-embed.css" type="text/css">
  <link rel="stylesheet" href="./includes/jsonview-core.css" type="text/css">
  <script type="text/javascript" async="" src="./includes/ga.js"></script><script type="text/javascript"></script>
  <script src="./includes/modernizr.min.js"></script>
  <script type="text/javascript" async="" src="./includes/ga.js"></script>
  <script type="text/javascript">
  
  // This is included here because other places don't have access to the pagename variable.
  var READTHEDOCS_DATA = {
    project: "stashboard",
    version: "latest",
    language: "en",
    page: "restapi",
    builder: "sphinx",
    theme: "sphinx_rtd_theme",
    docroot: "/docs/",
    source_suffix: ".rst",
    api_host: "https://readthedocs.org",
    commit: "3e4b18a8168c102d1e1d7f88fec22bcbfc530d23"
  }
  // Old variables
  var doc_version = "latest";
  var doc_slug = "stashboard";
  var page_name = "restapi";
  var html_theme = "sphinx_rtd_theme";
</script>
  <script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-17997319-1']);
  _gaq.push(['_trackPageview']);

  // User Analytics Code
  _gaq.push(['user._setAccount', 'None']);
  _gaq.push(['user._trackPageview']);
  // End User Analytics Code


  (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
     
  })();
</script>
</head>
<body class="wy-body-for-nav" role="document" cz-shortcut-listen="true">
  <div class="wy-grid-for-nav">
    <nav data-toggle="wy-nav-shift" class="wy-nav-side stickynav">
      <div class="wy-side-nav-search">
        <a href="index.php" class="fa fa-home"> <?=$project_name?></a>
      </div>
      <div class="wy-menu wy-menu-vertical" data-spy="affix" role="navigation" aria-label="main navigation">
        <ul class="current">
          <li class="toctree-l1 current"><a class="reference internal" href="#response-codes">Response Codes</a></li>
          <li class="toctree-l1"><a class="reference internal" href="#login">Login</a></li>
          <li class="toctree-l1"><a class="reference internal" href="#assign_orders">Assign Orders</a></li>
          <li class="toctree-l1"><a class="reference internal" href="#vieworders">View Order</a></li>
          <li class="toctree-l1"><a class="reference internal" href="#trackdriver">Driver Current status</a>
          </li>
          <li class="toctree-l1"><a class="reference internal" href="#updatedriverlocation">Update Driver location</a>
          </li>
          <li class="toctree-l1"><a class="reference internal" href="#droupoff">Droup Off</a></li>
          <li class="toctree-l1"><a class="reference internal" href="#return">Return</a></li>
          <li class="toctree-l1"><a class="reference internal" href="#reason">Reason list</a></li>
        </ul>
      </div>
    </nav>
    
    <section data-toggle="wy-nav-shift" class="wy-nav-content-wrap">
      <nav class="wy-nav-top" role="navigation" aria-label="top navigation">
        <i data-toggle="wy-nav-top" class="fa fa-bars"></i>
        <a href="index.php">Dashboard</a>
      </nav>
      
      <div class="wy-nav-content">
        <div class="rst-content">
          <div role="navigation" aria-label="breadcrumbs navigation">
            <ul class="wy-breadcrumbs">
              <li><a href="index.php"><?=$project_name?></a> »</li>
              <li>API Documentation</li>  
            </ul>
            <hr>
          </div>
  
          <div role="main" class="document">
            <div class="section" id="api-documentation">
              <h1>API Documentation<a class="headerlink" href="index.php#api-documentation" title="Permalink to this headline">¶</a></h1>
              <p>This doc explains response codes, request parameters and responses of this project's API.
              <!--<tt class="docutils literal"><span class="pre">/api/v1/</span></tt>-->
              </p>
              
              <!--Response Codes-->
                <div class="section" id="response-codes">
                  <h2>Response Codes<a class="headerlink" href="index.php#response-codes" title="Permalink to this headline">¶</a></h2>
                  <p>Response codes let you know that if action is successfully performed or not. If not than which problems occurred.</p>
                    <div class="wy-table-responsive">
                      <table border="1" class="docutils">
                        <colgroup>
                        <col width="21%">
                        <col width="79%">
                        </colgroup>
                        <thead valign="bottom">
                        <tr class="row-odd">
                          <th class="head">Code</th>
                          <th class="head">Text</th>
                          <th class="head">Description</th>
                        </tr>
                        </thead>
                        <tbody valign="top">
                        <tr class="row-even">
                          <td>200</td>
                          <td>OK</td>
                          <td>Success</td>
                        </tr>
                        <tr class="row-even">
                          <td>400</td>
                          <td>Bad Request</td>
                          <td>The request cannot be fulfilled due to bad syntax.</td>
                        </tr>
                        <tr class="row-even">
                          <td>401</td>
                          <td>Unauthorized</td>
                          <td>Authentication credentials were missing or incorrect.</td>
                        </tr>
                        <tr class="row-even">
                          <td>403</td>
                          <td>Forbidden</td>
                          <td>The request is understood, but it has been refused or access is not allowed.</td>
                        </tr>
                        <tr class="row-even">
                          <td>404</td>
                          <td>Not Found</td>
                          <td>The URI requested is invalid or the resource requested, such as a user, does not exists.</td>
                        </tr>
                        <tr class="row-even">
                          <td>500</td>
                          <td>Internal Server Error</td>
                          <td>Something is broken.</td>
                        </tr>
                        <tr class="row-even">
                          <td>601</td>
                          <td>Data Dupliacation</td>
                          <td>Let you know if provided data is already there.</td>
                        </tr>
                        <tr class="row-even">
                          <td>602</td>
                          <td>Could Not Save</td>
                          <td>Could not insert/update/delete record at the moment.</td>
                        </tr>
                        <tr class="row-even">
                          <td>603</td>
                          <td>No data found</td>
                          <td>No such data found.</td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                    
                </div>
            </div>
            
            <hr>
               
            <!--LOGIN API-->
            <div class="section" id="login">
              <h2>Login
                <a class="headerlink" href="index.php#login" title="Permalink to this headline">¶</a>
                <a href="<?=$api_path?>/login.php?lang=1&username=driver8&password=admin@1234" target="_blank" class="html_link">API Link</a>
              </h2>
              <p>Login to account with email id and password.</p>
                <h5>API LINK</h5>
                <div class="highlight-text">
                  <div class="highlight">
                    <pre><?=$api_path?>/login.php?lang=1&username=driver8&password=admin@1234</pre>
                  </div>
                </div>
                
                <h5>Request Parameters</h5>
                <div class="wy-table-responsive">
                  <table border="1" class="docutils">
                  <colgroup>
                    <col width="35%">
                    <col width="30%">
                    <col width="">
                  </colgroup>
                  <thead valign="bottom">
                  <tr class="row-odd">
                    <th class="head">Param</th>
                    <th class="head">Required/Optional</th>
                    <th class="head">Description</th>
                  </tr>
                  </thead>
                  <tbody valign="top">
                  <tr class="row-even">
                    <td>username</td>
                    <td>Required</td>
                    <td>User name</td>
                  </tr>
                  <tr class="row-odd">
                    <td>password</td>
                    <td>Required</td>
                    <td>password of user</td>
                  </tr>
                  </tbody>
                  </table>
                </div>
                <h5>Response</h5>
                  <h6><i class="fa fa-hand-o-right"></i> Success</h6>
                  <div class="highlight-js json"><div id="json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">code</span>: <span class="type-number">200</span>,</div></li><li><div class="hoverable"><span class="property">status</span>: <span class="type-string">"Success"</span>,</div></li><li><div class="hoverable"><span class="property">driver_id</span>: <span class="type-string">"24"</span>,</div></li><li><div class="hoverable"><span class="property">firstname</span>: <span class="type-string">"new"</span>,</div></li><li><div class="hoverable"><span class="property">lastname</span>: <span class="type-string">"driver1"</span>,</div></li><li><div class="hoverable"><span class="property">email</span>: <span class="type-string">"ewalltester12@gmail.com"</span>,</div></li><li><div class="hoverable"><span class="property">username</span>: <span class="type-string">"ew"</span></div></li></ul>}</div></div>
                  <h6><i class="fa fa-hand-o-right"></i> Error</h6>
                  <div class="highlight-js json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class=""><span class="property">code</span>: <span class="type-number">401</span>,</div></li><li><div class=""><span class="property">status</span>: <span class="type-string">"error"</span>,</div></li><li><div class=""><span class="property">message</span>: <span class="type-string">"No such user found"</span></div></li></ul>}</div>
            </div>
            
            
            
            <!--Assign Orders API-->
            <div class="section" id="assign_orders">
              <h2>Assign Orders
                <a class="headerlink" href="index.php#assign_orders" title="Permalink to this headline">¶</a>
                <a href="<?=$api_path?>/orderlist.php?driver_id=26&page=1&order_status=1" target="_blank" class="html_link">API Link</a>
              </h2>
              <p>Get Assign order details of driver.</p>
                <h5>API LINK</h5>
                <div class="highlight-text">
                  <div class="highlight">
                    <pre><?=$api_path?>/orderlist.php?driver_id=26&page=1&order_status=1</pre>
                  </div>
                </div>
                
                <h5>Request Parameters</h5>
                <div class="wy-table-responsive">
                  <table border="1" class="docutils">
                  <colgroup>
                    <col width="35%">
                    <col width="30%">
                    <col width="">
                  </colgroup>
                  <thead valign="bottom">
                  <tr class="row-odd">
                    <th class="head">Param</th>
                    <th class="head">Required/Optional</th>
                    <th class="head">Description</th>
                  </tr>
                  </thead>
                  <tbody valign="top">
                  <!--<tr class="row-even">-->
                  <!--   <td>lang</td>-->
                  <!--   <td>Required</td>-->
                  <!--   <td>1 => English , 4 =>Arabic </td>-->
                  <!-- </tr>-->
                  <tr class="row-even">
                    <td>driver_id</td>
                    <td>Required</td>
                    <td>Driver id</td>
                  </tr>
                  <tr class="row-even">
                    <td>page</td>
                    <td>Required</td>
                    <td>Page number for pagination</td>
                  </tr>
                  </tbody>
                  </table>
                </div>
                <h5>Response</h5>
                  <h6><i class="fa fa-hand-o-right"></i> Success</h6>
                  <div class="highlight-js json"><div id="json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">code</span>: <span class="type-number">200</span>,</div></li><li><div class="hoverable"><span class="property">status</span>: <span class="type-string">"Success"</span>,</div></li><li><div class="hoverable"><span class="property">Count</span>: <span class="type-number">3</span>,</div></li><li><div class="hoverable"><span class="property">order</span>: <div class="collapser"></div>[<span class="ellipsis"></span><ul class="array collapsible"><li><div class="hoverable"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">increment_id</span>: <span class="type-string">"100000063"</span>,</div></li><li><div class="hoverable"><span class="property">status</span>: <span class="type-string">"pending"</span>,</div></li><li><div class="hoverable"><span class="property">created_at</span>: <span class="type-string">"20/04/2017"</span>,</div></li><li><div class="hoverable hovered"><span class="property">ship_to</span>: <span class="type-string">"ewall test"</span>,</div></li><li><div class="hoverable"><span class="property">grand_total</span>: <span class="type-string">"4.50"</span></div></li></ul>},</div></li><li><div class="hoverable"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">increment_id</span>: <span class="type-string">"100000005"</span>,</div></li><li><div class="hoverable"><span class="property">status</span>: <span class="type-string">"pending"</span>,</div></li><li><div class="hoverable"><span class="property">created_at</span>: <span class="type-string">"15/04/2017"</span>,</div></li><li><div class="hoverable"><span class="property">ship_to</span>: <span class="type-string">"ewall test"</span>,</div></li><li><div class="hoverable"><span class="property">grand_total</span>: <span class="type-string">"65.00"</span></div></li></ul>},</div></li><li><div class="hoverable"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">increment_id</span>: <span class="type-string">"100000002"</span>,</div></li><li><div class="hoverable"><span class="property">status</span>: <span class="type-string">"need_approval"</span>,</div></li><li><div class="hoverable"><span class="property">created_at</span>: <span class="type-string">"15/04/2017"</span>,</div></li><li><div class="hoverable"><span class="property">ship_to</span>: <span class="type-string">"ewall test"</span>,</div></li><li><div class="hoverable"><span class="property">grand_total</span>: <span class="type-string">"52.00"</span></div></li></ul>}</div></li></ul>],</div></li><li><div class="hoverable"><span class="property">is_last</span>: <span class="type-number">1</span></div></li></ul>}</div></div>
                  <h6><i class="fa fa-hand-o-right"></i> Error</h6>
                  <div class="highlight-js json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class=""><span class="property">code</span>: <span class="type-number">401</span>,</div></li><li><div class=""><span class="property">status</span>: <span class="type-string">"error"</span>,</div></li><li><div class=""><span class="property">message</span>: <span class="type-string">"No such user found"</span></div></li></ul>}</div>
            </div>
              
            
            
            <!--View order API-->
            <div class="section" id="vieworders">
              <h2>View order
                <a class="headerlink" href="index.php#vieworders" title="Permalink to this headline">¶</a>
                <a href="<?=$api_path?>/orderview.php?increment_id=100000063&driver_id=26" target="_blank" class="html_link">API Link</a>
              </h2>
              <p>Get order Details</p>
                <h5>API LINK</h5>
                <div class="highlight-text">
                  <div class="highlight">
                    <pre><?=$api_path?>/orderview.php?increment_id=100000063&driver_id=26</pre>
                  </div>
                </div>
                
                <h5>Request Parameters</h5>
                <div class="wy-table-responsive">
                  <table border="1" class="docutils">
                  <colgroup>
                    <col width="35%">
                    <col width="30%">
                    <col width="">
                  </colgroup>
                  <thead valign="bottom">
                  <tr class="row-odd">
                    <th class="head">Param</th>
                    <th class="head">Required/Optional</th>
                    <th class="head">Description</th>
                  </tr>
                  </thead>
                  <tbody valign="top">
                  <!--<tr class="row-even">-->
                  <!--   <td>lang</td>-->
                  <!--   <td>Required</td>-->
                  <!--   <td>1 => English , 4 =>Arabic </td>-->
                  <!-- </tr>-->
                  <tr class="row-even">
                    <td>driver_id</td>
                    <td>Required</td>
                    <td>Driver id</td>
                  </tr>
                   <tr class="row-even">
                      <td>increment_id</td>
                      <td>Required</td>
                      <td>Order increment id.</td>
                    </tr>
                  </tbody>
                  </table>
                </div>
                <h5>Response</h5>
                  <h6><i class="fa fa-hand-o-right"></i> Success</h6>
                  <div class="highlight-js json"><div id="json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">code</span>: <span class="type-number">200</span>,</div></li><li><div class="hoverable"><span class="property">status</span>: <span class="type-string">"Success"</span>,</div></li><li><div class="hoverable"><span class="property">order</span>: <div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">increment_id</span>: <span class="type-string">"100000063"</span>,</div></li><li><div class="hoverable"><span class="property">status</span>: <span class="type-string">"pending"</span>,</div></li><li><div class="hoverable"><span class="property">created_at</span>: <span class="type-string">"20/04/2017"</span>,</div></li><li><div class="hoverable"><span class="property">subtotal</span>: <span class="type-string">"4.50"</span>,</div></li><li><div class="hoverable hovered"><span class="property">delivery_fee</span>: <span class="type-string">"0.00"</span>,</div></li><li><div class="hoverable"><span class="property">grand_total</span>: <span class="type-string">"4.50"</span>,</div></li><li><div class="hoverable"><span class="property">discount_label</span>: <span class="type-string">""</span>,</div></li><li><div class="hoverable"><span class="property">discount_amount</span>: <span class="type-string">"0.00"</span>,</div></li><li><div class="hoverable"><span class="property">Shipping Address</span>: <div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">name</span>: <span class="type-string">"ewall test"</span>,</div></li><li><div class="hoverable"><span class="property">city</span>: <span class="type-string">"chennai"</span>,</div></li><li><div class="hoverable"><span class="property">region</span>: <span class="type-string">"Array"</span>,</div></li><li><div class="hoverable"><span class="property">country</span>: <span class="type-string">"Kuwait"</span>,</div></li><li><div class="hoverable"><span class="property">street</span>: <span class="type-string">"1st cross street"</span>,</div></li><li><div class="hoverable"><span class="property">telephone</span>: <span class="type-string">"99999999"</span></div></li></ul>},</div></li><li><div class="hoverable"><span class="property">Delivery Method</span>: <div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">shipping_description</span>: <span class="type-string">"POS Shipping - Store Pickup"</span></div></li></ul>},</div></li><li><div class="hoverable"><span class="property">Payment Method</span>: <div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">name</span>: <span class="type-string">"Multiple Payments"</span></div></li></ul>},</div></li><li><div class="hoverable"><span class="property">product</span>: <div class="collapser"></div>[<span class="ellipsis"></span><ul class="array collapsible"><li><div class="hoverable"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">product_id</span>: <span class="type-string">"7"</span>,</div></li><li><div class="hoverable"><span class="property">product_name</span>: <span class="type-string">"French Pastry "</span>,</div></li><li><div class="hoverable"><span class="property">product_sku</span>: <span class="type-string">"010"</span>,</div></li><li><div class="hoverable"><span class="property">base_price</span>: <span class="type-string">"4.50"</span>,</div></li><li><div class="hoverable"><span class="property">product_qty</span>: <span class="type-string">"1"</span>,</div></li><li><div class="hoverable"><span class="property">row_total</span>: <span class="type-string">"4.50"</span></div></li></ul>}</div></li></ul>]</div></li></ul>}</div></li></ul>}</div></div>
                  <h6><i class="fa fa-hand-o-right"></i> Error</h6>
                  <div class="highlight-js json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class=""><span class="property">code</span>: <span class="type-number">401</span>,</div></li><li><div class=""><span class="property">status</span>: <span class="type-string">"error"</span>,</div></li><li><div class=""><span class="property">message</span>: <span class="type-string">"No such user found"</span></div></li></ul>}</div>
            </div>
            
            
            <!--Driver Current Location-->
            <div class="section" id="trackdriver">
              <h2>Driver Current Status
                <a class="headerlink" href="index.php#trackdriver" title="Permalink to this headline">¶</a>
                <a href="<?=$api_path?>/trackdriver.php?driver_id=26&increment_id=100000063" target="_blank" class="html_link">API Link</a>
              </h2>
              <p>driver current Status.</p>
                <h5>API LINK</h5>
                <div class="highlight-text">
                  <div class="highlight">
                    <pre><?=$api_path?>/trackdriver.php?driver_id=26&increment_id=100000063</pre>
                  </div>
                </div>
                
                <h5>Request Parameters</h5>
                <div class="wy-table-responsive">
                  <table border="1" class="docutils">
                  <colgroup>
                    <col width="35%">
                    <col width="30%">
                    <col width="">
                  </colgroup>
                  <thead valign="bottom">
                  <tr class="row-odd">
                    <th class="head">Param</th>
                    <th class="head">Required/Optional</th>
                    <th class="head">Description</th>
                  </tr>
                  </thead>
                  <tbody valign="top">
                  <!--<tr class="row-even">-->
                  <!--   <td>lang</td>-->
                  <!--   <td>Required</td>-->
                  <!--   <td>1 => English , 4 =>Arabic </td>-->
                  <!-- </tr>-->
                  <tr class="row-even">
                    <td>driver_id</td>
                    <td>Required</td>
                    <td>Driver id</td>
                  </tr>
                  <tr class="row-even">
                      <td>increment_id</td>
                      <td>Required</td>
                      <td>Order increment id.</td>
                    </tr>
                  </tbody>
                  </table>
                </div>
                <h5>Response</h5>
                  <h6><i class="fa fa-hand-o-right"></i> Success</h6>
                  <div class="highlight-js json"><div id="json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">code</span>: <span class="type-number">200</span>,</div></li><li><div class="hoverable"><span class="property">status</span>: <span class="type-string">"Success"</span>,</div></li><li><div class="hoverable"><span class="property">driver_id</span>: <span class="type-string">"24"</span>,</div></li><li><div class="hoverable"><span class="property">order_status</span>: <span class="type-string">"Delivered"</span>,</div></li><li><div class="hoverable"><span class="property">latitude</span>: <span class="type-string">"13.0649489"</span>,</div></li><li><div class="hoverable"><span class="property">longitude</span>: <span class="type-string">"80.232219"</span>,</div></li><li><div class="hoverable"><span class="property">user</span>: <div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">latitude</span>: <span class="type-string">"13.08268"</span>,</div></li><li><div class="hoverable"><span class="property">longitude</span>: <span class="type-string">"80.27072"</span></div></li></ul>}</div></li></ul>}</div></div>
                  <h6><i class="fa fa-hand-o-right"></i> Error</h6>
                  <div class="highlight-js json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class=""><span class="property">code</span>: <span class="type-number">401</span>,</div></li><li><div class=""><span class="property">status</span>: <span class="type-string">"error"</span>,</div></li><li><div class=""><span class="property">message</span>: <span class="type-string">"No such user found"</span></div></li></ul>}</div>
            </div>
            
            
            <!--Update Driver Location-->
            <div class="section" id="updatedriverlocation">
              <h2>Update Driver Location
                <a class="headerlink" href="index.php#updatedriverlocation" title="Permalink to this headline">¶</a>
                <a href="<?=$api_path?>/updatedriverlocation.php?driver_id=26&mobile=7845421254&latitude=12.0649489&longitude=75.232219" target="_blank" class="html_link">API Link</a>
              </h2>
              <p>Update Driver location.</p>
                <h5>API LINK</h5>
                <div class="highlight-text">
                  <div class="highlight">
                    <pre><?=$api_path?>/updatedriverlocation.php?driver_id=26&mobile=7845421254&latitude=12.0649489&longitude=75.232219</pre>
                  </div>
                </div>
                <h5>Request Parameters</h5>
                <div class="wy-table-responsive">
                  <table border="1" class="docutils">
                  <colgroup>
                    <col width="35%">
                    <col width="30%">
                    <col width="">
                  </colgroup>
                  <thead valign="bottom">
                  <tr class="row-odd">
                    <th class="head">Param</th>
                    <th class="head">Required/Optional</th>
                    <th class="head">Description</th>
                  </tr>
                  </thead>
                  <tbody valign="top">
                  <!--<tr class="row-even">-->
                  <!--   <td>lang</td>-->
                  <!--   <td>Required</td>-->
                  <!--   <td>1 => English , 4 =>Arabic </td>-->
                  <!-- </tr>-->
                  <tr class="row-even">
                    <td>driver_id</td>
                    <td>Required</td>
                    <td>Driver id</td>
                  </tr>
                  <tr class="row-even">
                    <td>mobile</td>
                    <td>Required</td>
                    <td>Driver mobile number</td>
                  </tr>
                  <tr class="row-even">
                      <td>latitude</td>
                      <td>Required</td>
                      <td>Driver current location latitude.</td>
                    </tr>
                  <tr class="row-even">
                      <td>longitude</td>
                      <td>Required</td>
                      <td>Driver current location longitude.</td>
                    </tr>
                  </tbody>
                  </table>
                </div>
                <h5>Response</h5>
                  <h6><i class="fa fa-hand-o-right"></i> Success</h6>
                  <div class="highlight-js json"><div id="json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">code</span>: <span class="type-number">200</span>,</div></li><li><div class="hoverable"><span class="property">status</span>: <span class="type-string">"Success"</span>,</div></li><li><div class="hoverable"><span class="property">driver_id</span>: <span class="type-string">"24"</span>,</div></li><li><div class="hoverable"><span class="property">success_message</span>: <span class="type-string">"Location Successfully updated."</span></div></li></ul>}</div></div>
                  <h6><i class="fa fa-hand-o-right"></i> Error</h6>
                  <div class="highlight-js json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class=""><span class="property">code</span>: <span class="type-number">603</span>,</div></li><li><div class=""><span class="property">status</span>: <span class="type-string">"error"</span>,</div></li><li><div class=""><span class="property">message</span>: <span class="type-string">"Error in update."</span></div></li></ul>}</div>
            </div>
            
            
            <!--Driver Droup off the order-->
            <div class="section" id="droupoff">
              <h2>Droup off
                <a class="headerlink" href="index.php#droupoff" title="Permalink to this headline">¶</a>
                <a href="<?=$api_path?>/droupoff.php" target="_blank" class="html_link">API Link</a>
              </h2>
              <p>Driver Droup off the order.</p>
                <h5>API LINK</h5>
                <div class="highlight-text">
                  <div class="highlight">
                    <pre><?=$api_path?>/droupoff.php</pre>
                  </div>
                </div>
                <h5>Request Parameters</h5>
                <div class="wy-table-responsive">
                  <table border="1" class="docutils">
                  <colgroup>
                    <col width="35%">
                    <col width="30%">
                    <col width="">
                  </colgroup>
                  <thead valign="bottom">
                  <tr class="row-odd">
                    <th class="head">Param</th>
                    <th class="head">Required/Optional</th>
                    <th class="head">Description</th>
                  </tr>
                  </thead>
                  <tbody valign="top">
                  <!--<tr class="row-even">
                     <td>lang</td>
                     <td>Required</td>
                     <td>1 => English , 4 =>Arabic </td>
                   </tr>-->
                  <tr class="row-even">
                    <td>driver_id</td>
                    <td>Required</td>
                    <td>Driver id (26)</td>
                  </tr>
                  <tr class="row-even">
                      <td>order_id</td>
                      <td>Required</td>
                      <td>Current order id. (100000063)</td>
                    </tr>
                  <tr class="row-even">
                      <td>received_name</td>
                      <td>Required</td>
                      <td>Order received customer name.</td>
                    </tr>
                  <tr class="row-even">
                      <td>sign</td>
                      <td>Required</td>
                      <td>user Signature.</td>
                    </tr>
                  
                  </tbody>
                  </table>
                </div>
                <h5>Response</h5>
                  <h6><i class="fa fa-hand-o-right"></i> Success</h6>
                  <div class="highlight-js json"><div id="json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">code</span>: <span class="type-number">200</span>,</div></li><li><div class="hoverable"><span class="property">status</span>: <span class="type-string">"Success"</span>,</div></li><li><div class="hoverable"><span class="property">driver_id</span>: <span class="type-string">"24"</span>,</div></li><li><div class="hoverable"><span class="property">success_message</span>: <span class="type-string">"Record Successfully added."</span></div></li></ul>}</div></div>
                  <h6><i class="fa fa-hand-o-right"></i> Error</h6>
                  <div class="highlight-js json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class=""><span class="property">code</span>: <span class="type-number">603</span>,</div></li><li><div class=""><span class="property">status</span>: <span class="type-string">"error"</span>,</div></li><li><div class=""><span class="property">message</span>: <span class="type-string">"Order Allredy shipped to the customer."</span></div></li></ul>}</div>
            </div>
            
            <!-- Return order request-->
            <div class="section" id="return">
              <h2>Return
                <a class="headerlink" href="index.php#return" title="Permalink to this headline">¶</a>
                <a href="<?=$api_path?>/return.php?driver_id=26&order_id=100000063&reason=Test&note=Test" target="_blank" class="html_link">API Link</a>
              </h2>
              <p>Return order request.</p>
                <h5>API LINK</h5>
                <div class="highlight-text">
                  <div class="highlight">
                    <pre><?=$api_path?>/droupoff.php?driver_id=26&order_id=100000063&reason=Test&note=Test</pre>
                  </div>
                </div>
                <h5>Request Parameters</h5>
                <div class="wy-table-responsive">
                  <table border="1" class="docutils">
                  <colgroup>
                    <col width="35%">
                    <col width="30%">
                    <col width="">
                  </colgroup>
                  <thead valign="bottom">
                  <tr class="row-odd">
                    <th class="head">Param</th>
                    <th class="head">Required/Optional</th>
                    <th class="head">Description</th>
                  </tr>
                  </thead>
                  <tbody valign="top">
                  <!--<tr class="row-even">-->
                  <!--   <td>lang</td>-->
                  <!--   <td>Required</td>-->
                  <!--   <td>1 => English , 4 =>Arabic </td>-->
                  <!-- </tr>-->
                  <tr class="row-even">
                    <td>driver_id</td>
                    <td>Required</td>
                    <td>Driver id</td>
                  </tr>
                  <tr class="row-even">
                      <td>order_id</td>
                      <td>Required</td>
                      <td>Current order id</td>
                    </tr>
                  <tr class="row-even">
                      <td>reason</td>
                      <td>Required</td>
                      <td>Return reason.</td>
                    </tr>
                  <tr class="row-even">
                      <td>note</td>
                      <td>Required</td>
                      <td>customer note.</td>
                    </tr>
                  
                  </tbody>
                  </table>
                </div>
                <h5>Response</h5>
                  <h6><i class="fa fa-hand-o-right"></i> Success</h6>
                  <div class="highlight-js json"><div id="json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">code</span>: <span class="type-number">200</span>,</div></li><li><div class="hoverable"><span class="property">status</span>: <span class="type-string">"Success"</span>,</div></li><li><div class="hoverable"><span class="property">driver_id</span>: <span class="type-string">"24"</span>,</div></li><li><div class="hoverable"><span class="property">success_message</span>: <span class="type-string">"Record Successfully added."</span></div></li></ul>}</div></div>
                  <h6><i class="fa fa-hand-o-right"></i> Error</h6>
                  <div class="highlight-js json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class=""><span class="property">code</span>: <span class="type-number">603</span>,</div></li><li><div class=""><span class="property">status</span>: <span class="type-string">"error"</span>,</div></li><li><div class=""><span class="property">message</span>: <span class="type-string">"Order Allredy shipped to the customer."</span></div></li></ul>}</div>
            </div>
            
            
            <!-- Return order request-->
            <div class="section" id="reason">
              <h2>Reason List
                <a class="headerlink" href="index.php#return" title="Permalink to this headline">¶</a>
                <a href="<?=$api_path?>/reason.php" target="_blank" class="html_link">API Link</a>
              </h2>
              <p>Reason List</p>
                <h5>API LINK</h5>
                <div class="highlight-text">
                  <div class="highlight">
                    <pre><?=$api_path?>/reason.php</pre>
                  </div>
                </div>
                <h5>Request Parameters</h5>
                <div class="wy-table-responsive">
                  <table border="1" class="docutils">
                  <colgroup>
                    <col width="35%">
                    <col width="30%">
                    <col width="">
                  </colgroup>
                  <thead valign="bottom">
                  <tr class="row-odd">
                    <th class="head">Param</th>
                    <th class="head">Required/Optional</th>
                    <th class="head">Description</th>
                  </tr>
                  </thead>
                  <tbody valign="top">
                  <!--<tr class="row-even">-->
                  <!--   <td>lang</td>-->
                  <!--   <td>Required</td>-->
                  <!--   <td>1 => English , 4 =>Arabic </td>-->
                  <!-- </tr>-->
                  </tbody>
                  </table>
                </div>
                <h5>Response</h5>
                  <h6><i class="fa fa-hand-o-right"></i> Success</h6>
                  <div class="highlight-js json"><div id="json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">code</span>: <span class="type-number">200</span>,</div></li><li><div class="hoverable hovered"><span class="property">status</span>: <span class="type-string">"Success"</span>,</div></li><li><div class="hoverable"><span class="property">reason</span>: <div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class="hoverable"><span class="property">Test-1</span>: <span class="type-string">"Test-1"</span>,</div></li><li><div class="hoverable"><span class="property">Test-2</span>: <span class="type-string">"Test-2"</span>,</div></li><li><div class="hoverable"><span class="property">Test-3</span>: <span class="type-string">"Test-3"</span>,</div></li><li><div class="hoverable"><span class="property">Test-4</span>: <span class="type-string">"Test-4"</span>,</div></li><li><div class="hoverable"><span class="property">Test-5</span>: <span class="type-string">"Test-5"</span></div></li></ul>}</div></li></ul>}</div></div>
                  <h6><i class="fa fa-hand-o-right"></i> Error</h6>
                  <div class="highlight-js json"><div class="collapser"></div>{<span class="ellipsis"></span><ul class="obj collapsible"><li><div class=""><span class="property">code</span>: <span class="type-number">400</span>,</div></li><li><div class=""><span class="property">status</span>: <span class="type-string">"error"</span>,</div></li><li><div class=""><span class="property">message</span>: <span class="type-string">"No record found."</span></div></li></ul>}</div>
            </div>
            
            
          </div>
        </div>
        
        
      <footer>
        <hr>
          <div class="rst-footer-buttons right" role="navigation" aria-label="footer navigation">
            <a target="_blank" href="http://www.dolphinwebsolution.com/">Dolphin web solution</a>
        </div>
        
      </footer>
    </div>
      
    </section>
</div>

<script type="text/javascript">
        var DOCUMENTATION_OPTIONS = {
            URL_ROOT:'./',
            VERSION:'1.5.0',
            COLLAPSE_INDEX:false,
            FILE_SUFFIX:'.html',
            HAS_SOURCE:  true
        };
    </script>
  <script type="text/javascript" src="./includes/jquery-2.0.3.min.js"></script>
  <!--<script type="text/javascript" src="./includes/jquery-migrate-1.2.1.min.js"></script>-->
  <!--<script type="text/javascript" src="./includes/underscore.js"></script>-->
  <!--<script type="text/javascript" src="./includes/doctools.js"></script>-->
  <!--<script type="text/javascript" src="./includes/readthedocs-doc-embed.js"></script>-->

  
<script type="text/javascript">
      jQuery(function () {
           $('.toctree-l1').click(function(){
            $('.current').removeClass('current');
            $(this).addClass('current');
          });
      });
  </script>

</body>
</html>