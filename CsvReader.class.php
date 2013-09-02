<?php

class CsvReader {
	public $fp;
	public $current_line;
	public $first_row_is_headers;
	public $headers;
	
	const FETCH_ASSOC = 1;
	const FETCH_NUM = 2;
	const FETCH_BOTH = 3;	
	const FETCH_OBJ = 4;	

	public function __construct($filename, $first_row_is_headers = true) {
		$this->fp = fopen($filename, 'r');
		$this->current_line = 0;
		$this->first_row_is_headers = $first_row_is_headers;
	}
	
	public function getHeaders() {
		if ($this->current_line === 0) {
			return $this->headers = $this->nextRow(self::FETCH_NUM, true);
		} else {
			if ($this->headers) {
				return $this->headers;
			} else {
				$f_location = ftell($this->fp);
				rewind($this->fp);
				$this->headers = $this->nextRow(self::FETCH_NUM, true);
				fseek($this->fp, $f_location);
				return $this->headers;				
			}
		}
	}
	
	public function nextRow($flags = self::FETCH_NUM, $getting_headers = false) {
		if ($this->current_line === 0 && $this->first_row_is_headers && !$getting_headers) {
			$this->getHeaders(); // need to ignore the first line
		}
		$this->current_line++;
		$row = fgetcsv($this->fp);
		if (!$row) {
			return false;			
		}
		switch ($flags) {
			case self::FETCH_ASSOC:
				return $this->formatRowWithHeader($row, $flags, $this->headers);
				break;
			case self::FETCH_NUM:
				return $row;
				break;
			case self::FETCH_OBJ:
				return (object) $this->formatRowWithHeader($row, $flags, $this->headers);
				break;
			case self::FETCH_BOTH:
				return array_merge($row, $this->formatRowWithHeader($row, $flags, $this->headers));
				break;
		}
	}
	
	public function allRows($flags = self::FETCH_NUM) {
		rewind($this->fp);
		$this->current_line = 0;
		$all_rows = array();
		while ($row = $this->nextRow($flags)) {
			$all_rows[] = $row;
		}
		return $all_rows;
	}

	private function formatRowWithHeader($row, $mode, $headers = null) {
		if (!$this->headers) {
			$this->getHeaders();
		}
		$return = array();
		foreach ($this->headers as $i => $header) {
			$return[$header] = $row[$i];
		}
		return $return;
	}
}

