CsvReader
=========

Usage:

`$csv = new CsvReader('file.csv', true); //has headers`
`$csv = new CsvReader('file.csv'); //does not have headers`

`echo 'headers => ' . print_r($csv->getHeaders(), true);`

`echo 'the "body" of the file =>' . print_pre($csv->allRows(CsvReader::FETCH_ASSOC));`
