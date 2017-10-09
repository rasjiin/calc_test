<?php 
class Product {
	public $price = 0;
	public $type = 'A';
	public $discounted = false;
	public function __construct($p, $t){
		$this->price = $p;
		$this->type = $t;
	}	
	public function SetDiscount($p){
		$this->price = $this->price - $this->price*$p;
		$this->discounted = true;
	}
}

class Calculator {
	private $prds = array();
	private $prdstypes = array();
	private $discountforqty = array();	
	public function CalcTotal( ...$products ){	
		$this->prds = $products;
		
		foreach($products as $product){
			$this->prdstypes[] = $product->type;
		}
		$this->SetDiscount('for2prd', 0.1, 'A', 'B');		
		$this->SetDiscount('for2prd', 0.05, 'D', 'E');		
		$this->SetDiscount('for3prd', 0.05, 'E', 'F', 'G');		
		$this->SetDiscount('for2arr', 0.05, 'A', ['K','L','M']);							
		$this->applyDiscountForQuantity();
		
		$total = 0;		
		foreach($this->prds as $product){
			$total = $total + $product->price;
		}
		echo $total;
	}
	private function applyDiscountForQuantity(){
		$products_count = count($this->prds);
		krsort($this->discountforqty);
		foreach($this->discountforqty as $k=>$val){
			if ($products_count >= $k){
				$qtydiscount = $val;
				break;
			}
		}		
		foreach($this->prds as $product){
			if (!$product->discounted) $product->SetDiscount($qtydiscount);
		}
	}
	public function setDiscountForQuantity($quantity, $discount){
		$this->discountforqty[$quantity] = $discount;
	}
	private function SetDiscount($discountType, $discount, $prd1, $prd2, $prd3 = null){
		switch ($discountType){
			case 'for2prd': 
				if (in_array($prd1, $this->prdstypes) && in_array($prd2, $this->prdstypes)){
					$cnt = array_count_values($this->prdstypes);
					$col = $cnt[$prd1]>$cnt[$prd2] ? $cnt[$prd2] : $cnt[$prd1];
					if ($col > 0){
						foreach($this->prds as $product1){
							if($product1->type == $prd1 && !$product1->discounted){
								foreach($this->prds as $product2){
									if($product2->type == $prd2  && !$product2->discounted){	
										$product1->SetDiscount($discount);
										$product2->SetDiscount($discount);
										$col--;
										if ($col == 0) break;
									}
								}							
							}
						}
					}
				}
				break;
			case 'for3prd': 				
				if ($prd3 != null && in_array($prd1, $this->prdstypes) && in_array($prd2, $this->prdstypes) && in_array($prd3, $this->prdstypes)){
					$cnt = array_count_values($this->prdstypes);
					$col = $cnt[$prd1]>$cnt[$prd2] ? $cnt[$prd2] : $cnt[$prd1];
					$col = $col>$cnt[$prd3] ? $cnt[$prd3] : $col;
					if ($col > 0){
						foreach($this->prds as $product1){
							if($product1->type == $prd1 && !$product1->discounted){
								foreach($this->prds as $product2){
									if($product2->type == $prd2  && !$product2->discounted){	
										foreach($this->prds as $product3){
											if($product3->type == $prd3  && !$product3->discounted){	
												$product1->SetDiscount($discount);
												$product2->SetDiscount($discount);
												$product3->SetDiscount($discount);
												$col--;
												if ($col == 0) break;
											}											
										}										
									}
								}							
							}
						}
					}
				}
				break;
			case 'for2arr': 
				if (in_array($prd1, $this->prdstypes) && is_array($prd2)){
					foreach($this->prds as $product1){
						if($product1->type == $prd1 && !$product1->discounted){
							foreach($prd2 as $producttype2){
								if (in_array($producttype2, $this->prdstypes)){
									foreach($this->prds as $product2){
										if($product2->type == $producttype2 && !$product2->discounted){
											$product1->SetDiscount($discount);
											$product2->SetDiscount($discount);
											break(2);
										}
									}
								}
							}
						}
					}
				}
				break;
		}		
	}
}

$A = new Product(10, 'A');
$B = new Product(20, 'B');
$C = new Product(10, 'C');
$D = new Product(40, 'D');
$E = new Product(30, 'E');
$F = new Product(30, 'F');
$G = new Product(30, 'G');
$H = new Product(30, 'H');
$I = new Product(30, 'I');
$J = new Product(30, 'J');
$K = new Product(30, 'K');
$L = new Product(30, 'L');
$M = new Product(30, 'M');

$calc = new Calculator();
$calc->setDiscountForQuantity(3, 0.05);
$calc->setDiscountForQuantity(4, 0.1);
$calc->setDiscountForQuantity(5, 0.2);
$calc->CalcTotal($A, $A, $B, $C, $D, $E, $L, $M);