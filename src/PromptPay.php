<?php
namespace W2W\CPP;

class PromptPay {
	const STD_PREFIX = "|";
	const STD_BRANCH_ID = "00000";
	
	public function generatePayload($sellertaxid, $reference1, $reference2 = null, $totalamount, $tranactiontype, $duedate, $quantity, $salesamount, $vatrate, $vatamount, $sellerbranch = null, $buyertaxid, $buyerbranch = null, $buyername, $reference3 = null, $proxyid, $proxytype, $netamount, $typeofincome, $taxrate, $taxamount, $taxcondition) {
		$data = [
			$this->v(self::STD_PREFIX),
			$this->v($sellertaxid),
			$this->v(PHP_EOL),
			$this->v($reference1),
			$this->v(PHP_EOL),
			$this->v($reference2),
			$this->v(PHP_EOL),
			$this->v($this->formatAmount($totalamount)),
			$this->v(PHP_EOL),
			$this->v($tranactiontype),
			$this->v(PHP_EOL),
			$this->v($duedate),
			$this->v(PHP_EOL),
			$this->v($quantity),
			$this->v(PHP_EOL),
			$this->v($this->formatAmount($salesamount)),
			$this->v(PHP_EOL),
			$this->v($this->formatPercent($vatrate)),
			$this->v(PHP_EOL),
			$this->v($this->formatAmount($vatamount)),
			$this->v(PHP_EOL),
			$this->v($sellerbranch == null ? self::STD_BRANCH_ID : $sellerbranch),
			$this->v(PHP_EOL),
			$this->v($buyertaxid),
			$this->v(PHP_EOL),
			$this->v($buyerbranch == null ? self::STD_BRANCH_ID : $buyerbranch),
			$this->v(PHP_EOL),
			$this->v($this->formatName($buyername)),
			$this->v(PHP_EOL),
			$this->v($reference3),
			$this->v(PHP_EOL),
			$this->v($proxyid),
			$this->v(PHP_EOL),
			$this->v($proxytype),
			$this->v(PHP_EOL),
			$this->v($this->formatAmount($netamount)),
			$this->v(PHP_EOL),
			$this->v($typeofincome),
			$this->v(PHP_EOL),
			$this->v($this->formatPercent($taxrate)),
			$this->v(PHP_EOL),
			$this->v($this->formatAmount($taxamount)),
			$this->v(PHP_EOL),
			$this->v($taxcondition)
		];
		
		return $this->serialize($data);
	}
	
	public function v($value) {
		return $value;
	}
	
	public function serialize($xs) {
		return implode('', $xs);
	}
	
	public function formatAmount($amount) {
		return number_format($amount, 2, "", "");
	}
	
	public function formatName($fullname) {
		if(mb_strlen($fullname) > 140) {
			$fullname = mb_substr($fullname, 0, 140, "utf-8");
		}
		
		return $fullname;
	}
	
	public function formatPercent($percent) {
		$percent = str_replace("%", "", $percent) / 100;
		$percent = number_format($percent, 2, "", "");
		$percent = str_pad($percent, 4, "0", STR_PAD_LEFT);
		
		return $percent;
	}
	
	public function crc16($data) {
		$crc16 = new \W2W\CRC\CRC16CCITT();
		$crc16->update($data);
		$checksum = $crc16->finish();
		
		return strtoupper(bin2hex($checksum));
	}
	
	public function generateQRCode($path, $sellertaxid, $reference1, $reference2 = null, $totalamount, $tranactiontype, $duedate, $quantity, $salesamount, $vatrate, $vatamount, $sellerbranch = null, $buyertaxid, $buyerbranch = null, $buyername, $reference3 = null, $proxyid, $proxytype, $netamount, $typeofincome, $taxrate, $taxamount, $taxcondition, $width = 500) {
		$payload = $this->generatePayload($sellertaxid, $reference1, $reference2, $totalamount, $tranactiontype, $duedate, $quantity, $salesamount, $vatrate, $vatamount, $sellerbranch, $buyertaxid, $buyerbranch, $buyername, $reference3, $proxyid, $proxytype, $netamount, $typeofincome, $taxrate, $taxamount, $taxcondition);
		$render = new \W2W\QRCode\Renderer\Image\Png();
		$render->setHeight($width);
		$render->setWidth($width);
		$render->setMargin(0);
		$writer = new \W2W\QRCode\Writer($render);
		
		header("Content-Type: image/png");
		
		echo $writer->writeString($payload);
	}
}