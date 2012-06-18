<?php
include 'header.php';
include 'configParameter.php';
include 'checkGETParams.php';

$articleID = $_GET['aid'];

// valuations of this product
$sql = "SELECT
			vlAnimal.valuationclassid, vlAnimal.nme, vlAnimal.description,
			vlEco.valuationclassid, vlEco.nme, vlEco.description,
			vlSocial.valuationclassid, vlSocial.nme, vlSocial.description

		FROM articleValuation av

		LEFT JOIN valuationclass_language vlAnimal ON av.animalprotection_vclassid = vlAnimal.valuationclassid
		LEFT JOIN valuationclass_language vlEco ON av.ecologic_vclassid = vlEco.valuationclassid
		LEFT JOIN valuationclass_language vlSocial ON av.social_vclassid = vlSocial.valuationclassid

		WHERE av.articleid = ?";

$stmt = $connect -> prepare($sql);
$stmt -> bind_param('i', $articleID);
$stmt -> execute();
$stmt -> bind_result($valuationID['animal'], $valuationName['animal'], $valuationDescription['animal'], $valuationID['eco'], $valuationName['eco'], $valuationDescription['eco'], $valuationID['social'], $valuationName['social'], $valuationDescription['social']);
$stmt -> fetch();
$stmt -> close();

/*
optional

while ($row = $result->fetch_object()) {
	$row->host;
}

-----

query: 	"company" on table c means the name of the merchant company
		"company" on table cl means the name of the manufacturer
*/

$sql = "SELECT
			a.nme, a.brand, a.pricefrom, a.substancedeclaredtxt,
			ast.gpSort,
			c.nme, c.infoGMO, cl.nme, cl.note_public,
			t.nme, t.countrycode

		FROM
			articleStatus ast,
			company c,
			territory t,
			article a

		LEFT JOIN company cl ON a.producerid = cl.id

		WHERE
			a.id = ?
			AND a.articleStatusID = ast.statusID
			AND a.salescompanyid = c.id
			AND a.territoryid = t.id";

$stmt = $connect -> prepare($sql);
$stmt -> bind_param('i', $articleID);
$stmt -> execute();
$stmt -> bind_result($articleName, $articleBrand, $articlePrice, $articleSubstance, $articleStatus, $companyName, $companyInfoGMO, $manufacturerName, $manufacturerNote, $territoryName, $territoryCountryCode);
$stmt -> fetch();
$stmt -> close();

$content .= '<div id="productDetail">
				<p class="productTitle">'.utf8_encode($articleName).'</p>
				<div id="productDetailHeader">
					<span>
						<a id="addProduct" href="addRemoveProduct.php?product='.$articleName.'&action=add"><img src="images/addProduct.png" alt="zur Einkaufliste"/></a>
						<p>Marke: '.utf8_encode($brand).'</p>
						<p>Preis: '.$articlePrice.'</p>
						<p>territory: '.$territoryName.', '.$territoryCountryCode.'</p>
						<p>';

// calculate valuation of each class
foreach($valuationID as $key => $value)
{
	$content .='<p></p>
				<div class="ratingContainer">'.$valuationNameCategory[$key].'</div>
				<div class="ratings">';

	for($i = 0; $i < 3; $i++)
	{
		if($value < 0)
		{
			$content .= '<img src="images/valuationRatingCircles/noRating.png" />';
		}
		else
		{
			$content .= '<img src="images/valuationRatingCircles/'.$value.'.png" />';
		}
	}

	$content .= '</div>';
}

$content .= '</span>
		</div>
	</div>
	<div id="productDetailNavigation">
		<a href="#">Bewertungen</a>
		<span>';

foreach($valuationName as $key => $value)
{
	$content .= '<p class="valuationCategory">'.ucfirst($key).'</p>';

	if($valuationID[$key] > 0)
		$content .= $valuationNameArray[utf8_encode($value)].'<p class="valuationDescription">'.utf8_encode($valuationDescription[$key]).'</p>';
	else
		$content .= '<p class="valuationNotFound">keine Bewertung</p>';
}

$content .= '</span>';

if(!empty($manufacturerName))
	$content .= '
		<a href="#">Hersteller</a>
		<span>
			<p class="valuationCategory">'.utf8_encode($manufacturerName).'</p>
			<p class="toCenter">'.utf8_encode($manufacturerNote).'</p>
		</span>';

if(!empty($articleSubstance))
	$content .= '
		<a href="#">Inhaltsstoffe</a>
		<span>
			<p class="toCenter">'.utf8_encode($articleSubstance).'</p>
		</span>';

if(!empty($articleSubstance))
	$content .= '
		<a href="#">Vertreiber</a>
		<span>
			<p class="valuationCategory">'.utf8_encode($companyName).'</p>
			<p class="toCenter"><strong>InfoGMO</strong><br /> '.utf8_encode($companyInfoGMO).'</p>
		</span>
		';

$content .= '</div>	';

include 'footer.php';
?>
