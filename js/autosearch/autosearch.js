function getproductdetail()
{
	var _location = document.location.toString();
	var applicationNameIndex = _location.indexOf('/', _location.indexOf('://') + 3);
	var applicationName = _location.substring(0, applicationNameIndex) + '/';
	var webFolderIndex = _location.indexOf('/', _location.indexOf(applicationName) + applicationName.length);
	var webFolderFullPath = _location.substring(0, webFolderIndex);
	//alert(webFolderFullPath+'/index.php/deal/adminhtml_deal/getProductDetail');
	productid = $('product_id').getValue();
	new Ajax.Request(webFolderFullPath+'/index.php/deal/adminhtml_deal/getProductDetail', {
	  method:'post',
	  parameters: {productid: productid},
	  onSuccess: function(transport) {
	    var response = transport.responseText || "Please Select any Product";
		$('update').update(response);
		getcalculatedprice();
	  },
	  onFailure: function() { alert('Something went wrong...'); }
	});
}
document.observe("dom:loaded", function() {
	getproductdetail();
	showimage();
	  
	});
function showdiscountelement(currentvalue)
{
	$('deal_price').setValue("");
	if(currentvalue == 1)
	{
		$('discount_amount').show();
		$('discount').hide();
	}else
	{
		$('discount').show();
		$('discount_amount').hide();
	}
}
function showimage()
{
	currentstyle = $('style').getValue();
	var _location = document.location.toString();
	var applicationNameIndex = _location.indexOf('/', _location.indexOf('://') + 3);
	var applicationName = _location.substring(0, applicationNameIndex) + '/';
	var webFolderIndex = _location.indexOf('/', _location.indexOf(applicationName) + applicationName.length);
	var webFolderFullPath = _location.substring(0, webFolderIndex);
	if(currentstyle == 1)
	{
		var imgs = $('sampleimage').getElementsBySelector('img');
		imgs.each(function(img) {
		    img.src = webFolderFullPath+'/js/images/boring.png';
		});

	}else
	{
		var imgs = $('sampleimage').getElementsBySelector('img');
		imgs.each(function(img) {
		img.src = webFolderFullPath+'/js/images/flip.png';
		});	
	}
}

function getcalculatedprice()
{
	
	if($('discount_type').getValue() == 1)
	{
		$('deal_price').setValue("");
		elementvalue = $('discount_amount').getValue();
		specialprice = (parseInt($('specialprice').innerHTML))
		$dealprice = (specialprice-elementvalue);
		$('deal_price').setValue($dealprice);

		$('discount').setValue("");
	}else
	{
		
		$('deal_price').setValue("");
		elementvalue = $('discount').getValue();
		specialprice = ($('specialprice').innerHTML == null) ? "" : parseInt($('specialprice').innerHTML);
		$discount = ((specialprice*elementvalue)/100);
		$dealsprice = specialprice-$discount;
		$('deal_price').setValue($dealsprice);
		//$('discount').setValue(elementvalue);
		$('discount_amount').setValue("");
		
	}
}

