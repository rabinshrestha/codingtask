<?php namespace App\Http\Controllers;
ini_set('max_execution_time', 300);

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        function getDefaultPrice($data){
            $property = NULL;
            foreach ($data as $key => $value) {
                // dd($value);
                if($value['default'])
                    return $value['property_price'];
                $property = $value;
            }
            //if no default value set then send which has property price as 0
            $allSame = TRUE;
            $previousPropertyPrice = NULL;
            foreach ($data as $key => $value) {
                // dd($value);
                if(($previousPropertyPrice != NULL) && ($previousPropertyPrice != $value['property_price']))
                {
                    $allSame = FALSE;
                }
                if($value['property_price'] == 0)
                    return $value['property_price'];
                $previousPropertyPrice = $value['property_price'];
                // $property = $value;
            }
            if($previousPropertyPrice)
                return $previousPropertyPrice;
            // if all property value are same then return any one
            echo "No default property price: ";
            dd($property);
        }

        // $filePath = storage_path('updated_price_short.xlsx');
        $filePath = storage_path('updated_price1.xlsx');

        $data = Excel::load($filePath, function($reader) {
            $reader = $reader->take(100);
            $reader->each(function($sheet) {
                $products = array();
                $productCommonData = array();
                $previousProductBaseUrl = NULL;
                $productBaseUrl = NULL;
                $productProperty = array();
                $outputFilePath ='created_price';
                $fileWritableProduct = array();
                $productImages = NULL;
                $propertyImages = array();

                // Loop through all rows
                $sheet->each(function($row) use (&$products, &$previousProductBaseUrl, &$productBaseUrl, &$productProperty, &$productCommonData, $outputFilePath, &$fileWritableProduct, &$productImages, &$propertyImages) {
                    $productBaseUrl = $row->base_url;
                    $productPropertyGroup = $row->property_group;

                    if(empty($productCommonData)){
                        // echo 'empty it is';
                        $categoryValues = array($row->category_name_1, $row->category_name_2, $row->category_name_3, $row->category_name_4, $row->category_name_5, $row->category_name_6, $row->category_name_7, $row->category_name_8, $row->category_name_9, $row->category_name_10, $row->category_name_11, $row->category_name_12);

                        $productColors = explode(',', $row->color);
                        $productRanges = explode(',', $row->ranges);
                        $productMaterials = explode(',', $row->materials);
                        $categoryValues = array_merge($categoryValues, $productColors, $productRanges, $productMaterials);
                        // $categoryValues = array_merge($categoryValues, $productColors);

                        $categoryValues = array_filter($categoryValues, function($data){
                            return !empty($data);
                        });

                        $productImages = $row->images;
                        $productImages = array_unique(explode(';', $productImages));
                        $productCommonData = array(
                            'base_url'  =>  $productBaseUrl,
                            'ranges'    =>  $row->ranges,
                            'sku'       =>  $row->sku,
                            'meta_title'=>  $row->meta_title,
                            'detail'    =>  $row->detail,
                            'images_alt'=>  $row->images_alt,
                            'slug'      =>  $row->slug,
                            'category_name' =>  implode(',', $categoryValues),
                            'images'        =>  $productImages,
                            'meta_description'  =>  $row->meta_description,
                            'color'             =>  $row->color,
                            'weight'            =>  $row->weight,
                        );
                        // dump($productCommonData);
                        // die;

                    }
                    //first row of file or same product fetched
                    if(empty($products) || ($previousProductBaseUrl == $productBaseUrl)){
                        $products[] = array(
                            'base_url'  => $row->base_url,
                        );
                        
                        $productProperty[$productPropertyGroup][] = array(
                            'property_group'    =>  $row->property_group,
                            'property_name'     =>  $row->property_name,
                            'price'             =>  $row->price, 
                            'property_price'    =>  $row->property_price,
                            'default'           =>  $row->default,
                        );
                        if(!empty($row->property_image))
                            $propertyImages[] = $row->property_image;
                    }
                    //diferent product fetched. So work on available product
                    else{
                        //manipulate previously generated data
                        $finalWritableProduct = array();
                        $productPropertyGroupData = current($productProperty);
                        $nextProductPropertyGroupData = next($productProperty);
                        $lastProductPropertyGroupData = next($productProperty);
                        $productPrice = NULL;
                        $firstDefault = $nextDefault = $lastDefault = NULL;
                        $SKUCount = 1;
                        foreach ($productPropertyGroupData as $firstKey => $firstValue) {
                            if($nextProductPropertyGroupData != false){
                                foreach ($nextProductPropertyGroupData as $nextKey => $nextValue) {
                                    if($lastProductPropertyGroupData != false){
                                        foreach ($lastProductPropertyGroupData as $lastKey => $lastValue) {
                                            if($firstDefault == NULL){
                                                $firstDefault = getDefaultPrice($productPropertyGroupData);
                                                $nextDefault = getDefaultPrice($nextProductPropertyGroupData);
                                                $lastDefault = getDefaultPrice($lastProductPropertyGroupData);
                                            }
                                            $initialProductPrice = $firstValue['price'];

                                            $productPrice = $initialProductPrice - $firstDefault - $nextDefault - $lastDefault + $firstValue['property_price'] + $nextValue['property_price'] + $lastValue['property_price'];

                                            $finalWritableProduct[] = array(
                                                'Handle'        =>  $productCommonData['base_url'], 
                                                'Title'         =>  $productCommonData['meta_title'], 
                                                'Body (HTML)'   =>  $productCommonData['detail'], 
                                                'Vendor'        =>  '',
                                                'Type'          =>  $productCommonData['meta_title'], 
                                                'Tags'          =>  $productCommonData['category_name'],
                                                'Published'     =>  'true',
                                                'Option1 Name'  =>  $firstValue['property_group'],
                                                'Option1 Value' =>  $firstValue['property_name'],
                                                'Option2 Name'  =>  $nextValue['property_group'],
                                                'Option2 Value' =>  $nextValue['property_name'],
                                                'Option3 Name'  =>  $lastValue['property_group'],
                                                'Option3 Value' =>  $lastValue['property_name'],
                                                'Variant SKU'   =>  'SH'.$SKUCount.$productCommonData['sku'], 
                                                'Variant Grams' =>  $productCommonData['weight'], 
                                                'Variant Inventory Tracker' =>  'shopify',
                                                'Variant Inventory Qty'     =>  '9999',
                                                'Variant Inventory Policy'  =>  'deny',
                                                'Variant Fulfillment Service'   =>  'manual',
                                                'Variant Price'                 =>  $productPrice,
                                                'Variant Compare At Price'      =>  '',
                                                'Variant Requires Shipping'     =>  'true',
                                                'Variant Taxable'  => 'false',
                                                'Variant Barcode'  => '',
                                                'Image Src'  => '',
                                                'Image Alt Text'  => $productCommonData['images_alt'], 
                                                'Gift Card'  => 'false',
                                                'SEO Title'  => $productCommonData['meta_title'], 
                                                'SEO Description'  => $productCommonData['meta_description'], 
                                                'Google Shopping / Google Product Category' => '',
                                                'Google Shopping / Gender'  => '',
                                                'Google Shopping / Age Group'  => '',
                                                'Google Shopping / MPN'  => '',
                                                'Google Shopping / AdWords Grouping'  => '',
                                                'Google Shopping / AdWords Labels'  => '',
                                                'Google Shopping / Condition'  => '',
                                                'Google Shopping / Custom Product'  => '',
                                                'Google Shopping / Custom Label 0'  => '',
                                                'Google Shopping / Custom Label 1'  => '',
                                                'Google Shopping / Custom Label 2'  => '',
                                                'Google Shopping / Custom Label 3'  => '',
                                                'Google Shopping / Custom Label 4'  => '',
                                                'Variant Image'  => '',
                                                'Variant Weight Unit'  => 'kg',
                                                'Variant Tax Code'  => '',

                                            );
                                            $SKUCount++;
                                            // $finalWritableProduct[] = array(
                                            //     'base_url'  =>  $productCommonData['base_url'], 
                                            //     'slug'      =>  $productCommonData['slug'],
                                            //     'sku'       =>  $productCommonData['sku'], 
                                            //     'ranges'    =>  $productCommonData['ranges'], 
                                            //     'property1' =>  $firstValue['property_group'],
                                            //     'property1 value'   =>  $firstValue['property_name'],
                                            //     'property2'         =>  $nextValue['property_group'],
                                            //     'property2 value'   =>  $nextValue['property_name'],
                                            //     'property3'         =>  $lastValue['property_group'],
                                            //     'property3 value'   =>  $lastValue['property_name'],
                                            //     'price'             =>  $productPrice,
                                            //     'category_name'     =>  $productCommonData['category_name'],
                                            // );
                                        }
                                    }
                                    else{
                                        if($firstDefault == NULL){
                                                $firstDefault = getDefaultPrice($productPropertyGroupData);
                                                $nextDefault = getDefaultPrice($nextProductPropertyGroupData);
                                                // $lastDefault = getDefaultPrice($lastProductPropertyGroupData);
                                        }
                                        $initialProductPrice = $firstValue['price'];

                                        $productPrice = $initialProductPrice - $firstDefault - $nextDefault + $firstValue['property_price'] + $nextValue['property_price'];

                                        $finalWritableProduct[] = array(
                                            'Handle'        =>  $productCommonData['base_url'], 
                                            'Title'         =>  $productCommonData['meta_title'], 
                                            'Body (HTML)'   =>  $productCommonData['detail'], 
                                            'Vendor'        =>  '',
                                            'Type'          =>  $productCommonData['meta_title'], 
                                            'Tags'          =>  $productCommonData['category_name'],
                                            'Published'     =>  'true',
                                            'Option1 Name'  =>  $firstValue['property_group'],
                                            'Option1 Value' =>  $firstValue['property_name'],
                                            'Option2 Name'  =>  $nextValue['property_group'],
                                            'Option2 Value' =>  $nextValue['property_name'],
                                            'Option3 Name'  =>  '',//$lastValue['property_group'],
                                            'Option3 Value' =>  '',//$lastValue['property_name'],
                                            'Variant SKU'   =>  'SH'.$SKUCount.$productCommonData['sku'], 
                                            'Variant Grams' =>  $productCommonData['weight'], 
                                            'Variant Inventory Tracker' =>  'shopify',
                                            'Variant Inventory Qty'     =>  '9999',
                                            'Variant Inventory Policy'  =>  'deny',
                                            'Variant Fulfillment Service'   =>  'manual',
                                            'Variant Price'                 =>  $productPrice,
                                            'Variant Compare At Price'      =>  '',
                                            'Variant Requires Shipping'     =>  'true',
                                            'Variant Taxable'  => 'false',
                                            'Variant Barcode'  => '',
                                            'Image Src'  => '',
                                            'Image Alt Text'  => $productCommonData['images_alt'], 
                                            'Gift Card'  => 'false',
                                            'SEO Title'  => $productCommonData['meta_title'], 
                                            'SEO Description'  => $productCommonData['meta_description'], 
                                            'Google Shopping / Google Product Category' => '',
                                            'Google Shopping / Gender'  => '',
                                            'Google Shopping / Age Group'  => '',
                                            'Google Shopping / MPN'  => '',
                                            'Google Shopping / AdWords Grouping'  => '',
                                            'Google Shopping / AdWords Labels'  => '',
                                            'Google Shopping / Condition'  => '',
                                            'Google Shopping / Custom Product'  => '',
                                            'Google Shopping / Custom Label 0'  => '',
                                            'Google Shopping / Custom Label 1'  => '',
                                            'Google Shopping / Custom Label 2'  => '',
                                            'Google Shopping / Custom Label 3'  => '',
                                            'Google Shopping / Custom Label 4'  => '',
                                            'Variant Image'  => '',
                                            'Variant Weight Unit'  => 'kg',
                                            'Variant Tax Code'  => '',

                                        );
                                        $SKUCount++;
                                        // $finalWritableProduct[] = array(
                                        //     'base_url'  =>  $productCommonData['base_url'], 
                                        //     'slug'      =>  $productCommonData['slug'],
                                        //     'sku'       =>  $productCommonData['sku'], 
                                        //     'ranges'    =>  $productCommonData['ranges'], 
                                        //     'property1' =>  $firstValue['property_group'],
                                        //     'property1 value'   =>  $firstValue['property_name'],
                                        //     'property2'         =>  $nextValue['property_group'],
                                        //     'property2 value'   =>  $nextValue['property_name'],
                                        //     'property3'         =>  '',
                                        //     'property3 value'   =>  '',
                                        //     'price'             =>  $productPrice,
                                        //     'category_name'     =>  $productCommonData['category_name'],
                                        // );
                                    }
                                }
                            }
                            //only one type product property 
                            else{
                                if($firstDefault == NULL){
                                        $firstDefault = getDefaultPrice($productPropertyGroupData);
                                        // $nextDefault = getDefaultPrice($nextProductPropertyGroupData);
                                        // $lastDefault = getDefaultPrice($lastProductPropertyGroupData);
                                    }
                                    $initialProductPrice = $firstValue['price'];

                                    $productPrice = $initialProductPrice - $firstDefault + $firstValue['property_price'];

                                    $finalWritableProduct[] = array(
                                        'Handle'        =>  $productCommonData['base_url'], 
                                        'Title'         =>  $productCommonData['meta_title'], 
                                        'Body (HTML)'   =>  $productCommonData['detail'], 
                                        'Vendor'        =>  '',
                                        'Type'          =>  $productCommonData['meta_title'], 
                                        'Tags'          =>  $productCommonData['category_name'],
                                        'Published'     =>  'true',
                                        'Option1 Name'  =>  $firstValue['property_group'],
                                        'Option1 Value' =>  $firstValue['property_name'],
                                        'Option2 Name'  =>  '',//$nextValue['property_group'],
                                        'Option2 Value' =>  '',//$nextValue['property_name'],
                                        'Option3 Name'  =>  '',//$lastValue['property_group'],
                                        'Option3 Value' =>  '',//$lastValue['property_name'],
                                        'Variant SKU'   =>  'SH'.$SKUCount.$productCommonData['sku'], 
                                        'Variant Grams' =>  $productCommonData['weight'], 
                                        'Variant Inventory Tracker' =>  'shopify',
                                        'Variant Inventory Qty'     =>  '9999',
                                        'Variant Inventory Policy'  =>  'deny',
                                        'Variant Fulfillment Service'   =>  'manual',
                                        'Variant Price'                 =>  $productPrice,
                                        'Variant Compare At Price'      =>  '',
                                        'Variant Requires Shipping'     =>  'true',
                                        'Variant Taxable'  => 'false',
                                        'Variant Barcode'  => '',
                                        'Image Src'  => '',
                                        'Image Alt Text'  => $productCommonData['images_alt'], 
                                        'Gift Card'  => 'false',
                                        'SEO Title'  => $productCommonData['meta_title'], 
                                        'SEO Description'  => $productCommonData['meta_description'], 
                                        'Google Shopping / Google Product Category' => '',
                                        'Google Shopping / Gender'  => '',
                                        'Google Shopping / Age Group'  => '',
                                        'Google Shopping / MPN'  => '',
                                        'Google Shopping / AdWords Grouping'  => '',
                                        'Google Shopping / AdWords Labels'  => '',
                                        'Google Shopping / Condition'  => '',
                                        'Google Shopping / Custom Product'  => '',
                                        'Google Shopping / Custom Label 0'  => '',
                                        'Google Shopping / Custom Label 1'  => '',
                                        'Google Shopping / Custom Label 2'  => '',
                                        'Google Shopping / Custom Label 3'  => '',
                                        'Google Shopping / Custom Label 4'  => '',
                                        'Variant Image'  => '',
                                        'Variant Weight Unit'  => 'kg',
                                        'Variant Tax Code'  => '',

                                    );
                                    $SKUCount++;
                                    // $finalWritableProduct[] = array(
                                    //     'base_url'  =>  $productCommonData['base_url'], 
                                    //     'slug'      =>  $productCommonData['slug'],
                                    //     'sku'       =>  $productCommonData['sku'], 
                                    //     'ranges'    =>  $productCommonData['ranges'], 
                                    //     'property1' =>  $firstValue['property_group'],
                                    //     'property1 value'   =>  $firstValue['property_name'],
                                    //     'property2'         =>  '',
                                    //     'property2 value'   =>  '',
                                    //     'property3'         =>  '',
                                    //     'property3 value'   =>  '',
                                    //     'price'             =>  $productPrice,
                                    //     'category_name'     =>  $productCommonData['category_name'],
                                    // );
                            }
                            
                        }

                        //add image data to final writable product
                        $propertyImages = array_unique($propertyImages);
                        foreach ($finalWritableProduct as $key => $value) {
                            if(isset($productCommonData['images'][$key])){
                                $finalWritableProduct[$key]['Image Src'] = $productCommonData['images'][$key];
                            }
                            else{
                                $finalWritableProduct[$key]['Image Src'] = '';
                            }

                            // for property image
                            if(isset($propertyImages[$key])){
                                $finalWritableProduct[$key]['Variant Image'] = $propertyImages[$key];
                            }
                            else{
                                $finalWritableProduct[$key]['Variant Image'] = '';
                            }

                            //set image alt text to empty if no image src
                            if(empty($finalWritableProduct[$key]['Image Src'])){
                                $finalWritableProduct[$key]['Image Alt Text'] = '';
                            }
                        }
                        // if image count is more than product count
                        $moreImageCount = count($productCommonData['images']) - count($finalWritableProduct);
                        $finalWritableProductCount = count($finalWritableProduct);
                        if($moreImageCount > 0){
                            // add more product row with image only
                            for($i=1; $i<=$moreImageCount; $i++){

                                $finalWritableProduct[] = array(
                                    'Handle'        =>  '',//$productCommonData['base_url'], 
                                    'Title'         =>  '',//$productCommonData['meta_title'], 
                                    'Body (HTML)'   =>  '',//$productCommonData['meta_description'], 
                                    'Vendor'        =>  '',//'',
                                    'Type'          =>  '',//$productCommonData['meta_title'], 
                                    'Tags'          =>  '',//$productCommonData['color'],
                                    'Published'     =>  '',//'true',
                                    'Option1 Name'  =>  '',//$firstValue['property_group'],
                                    'Option1 Value' =>  '',//$firstValue['property_name'],
                                    'Option2 Name'  =>  '',//$nextValue['property_group'],
                                    'Option2 Value' =>  '',//$nextValue['property_name'],
                                    'Option3 Name'  =>  '',//'',//$lastValue['property_group'],
                                    'Option3 Value' =>  '',//'',//$lastValue['property_name'],
                                    'Variant SKU'   =>  '',//$productCommonData['sku'], 
                                    'Variant Grams' =>  '',//$productCommonData['weight'], 
                                    'Variant Inventory Tracker' =>  '',//'',
                                    'Variant Inventory Qty'     =>  '',//'9999',
                                    'Variant Inventory Policy'  =>  '',//'deny',
                                    'Variant Fulfillment Service'   =>  '',//'manual',
                                    'Variant Price'                 =>  '',//$productPrice,
                                    'Variant Compare At Price'      =>  '',//'',
                                    'Variant Requires Shipping'     =>  '',//'true',
                                    'Variant Taxable'   => '',//'false',
                                    'Variant Barcode'   => '',//'',
                                    'Image Src'         => $productCommonData['images'][$finalWritableProductCount + $i -1],
                                    'Image Alt Text'    => '',//$productCommonData['images_alt'], 
                                    'Gift Card'         => '',//'false',
                                    'SEO Title'         => '',//$productCommonData['meta_title'], 
                                    'SEO Description'   => '',//$productCommonData['meta_description'], 
                                    'Google Shopping / Google Product Category' => '',
                                    'Google Shopping / Gender'  => '',
                                    'Google Shopping / Age Group'  => '',
                                    'Google Shopping / MPN'  => '',
                                    'Google Shopping / AdWords Grouping'  => '',
                                    'Google Shopping / AdWords Labels'  => '',
                                    'Google Shopping / Condition'  => '',
                                    'Google Shopping / Custom Product'  => '',
                                    'Google Shopping / Custom Label 0'  => '',
                                    'Google Shopping / Custom Label 1'  => '',
                                    'Google Shopping / Custom Label 2'  => '',
                                    'Google Shopping / Custom Label 3'  => '',
                                    'Google Shopping / Custom Label 4'  => '',
                                    'Variant Image'  => '',
                                    'Variant Weight Unit'  => '',//'kg',
                                    'Variant Tax Code'  => '',

                                );
                                // $finalWritableProduct[] = array(
                                //     'base_url'  =>  '',//$productCommonData['base_url'], 
                                //     'slug'      =>  '',//$productCommonData['slug'],
                                //     'sku'       =>  '',//$productCommonData['sku'], 
                                //     'ranges'    =>  '',//$productCommonData['ranges'], 
                                //     'property1' =>  '',//$firstValue['property_group'],
                                //     'property1 value'   =>  '',//$firstValue['property_name'],
                                //     'property2'         =>  '',//$nextValue['property_group'],
                                //     'property2 value'   =>  '',//$nextValue['property_name'],
                                //     'property3'         =>  '',//$lastValue['property_group'],
                                //     'property3 value'   =>  '',//$lastValue['property_name'],
                                //     'price'             =>  '',//$productPrice,
                                //     'category_name'     =>  '',//$productCommonData['category_name'],
                                //     'images'            =>  $productCommonData['images'][$finalWritableProductCount + $i -1],
                                //     'property_image'    =>  '',
                                // );

                            }
                        }
                        // dump($finalWritableProduct);
                        // dump($propertyImages);
                        array_push($fileWritableProduct, $finalWritableProduct);                  
                        $productCommonData = array();

                        //set property image value
                        $propertyImages = array();
                        if(!empty($row->property_image))
                            $propertyImages[] = $row->property_image;

                        //reset product and product property count
                        $products = array();
                        $productProperty = array();
                        unset($categoryValues);
                        unset($productPropertyGroupData);
                        unset($nextProductPropertyGroupData);
                        unset($lastProductPropertyGroupData);
                        
                        $productProperty[$productPropertyGroup][] = array(
                            'property_group'    =>  $row->property_group,
                            'property_name'     =>  $row->property_name,
                            'price'             =>  $row->price, 
                            'property_price'    =>  $row->property_price,
                            'default'           =>  $row->default,
                        );
                    }

                    $previousProductBaseUrl = $row->base_url;
                    $productBaseUrl = $row->base_url;
                });
                $fileWritableProduct = call_user_func_array('array_merge', $fileWritableProduct);
                Excel::create($outputFilePath, function($excel) use($fileWritableProduct) {
                    $excel->sheet('Sheet1', function($sheet) use ($fileWritableProduct) {
                        $sheet->fromArray($fileWritableProduct);
                    });
                })->store('xls');
                dump($fileWritableProduct);
            });
        });
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

    public function _dd($data, $die = TRUE){
        echo "<pre>";
        print_r($data);
        $die && die;

    }

}
