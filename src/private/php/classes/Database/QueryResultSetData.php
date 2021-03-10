<?php
  namespace ShiftCodesTK\Database;

  /** Represents the *Result Set Data* of a Database Query */
  class QueryResultSetData {
    /**
     * @var int The maximum number of items in a Result Set Chunk.
     */
    public $max_chunk_size = null;
    /**
     * @var int The number of items in the current Result Set Chunk.
     */
    public $current_chunk_size = null;
    /**
     * @var int The total number of items in all Result Set Chunks.
     */
    public $total_chunk_size = null;
    /**
     * @var int The current chunk number. Starts at **1**, up to the value of `$total_chunks`.
     */
    public $current_chunk = null;
    /**
     * @var int The total number of chunks.
     */
    public $total_chunks = null;
    /**
     * @var bool Indicates if a previous Result Set Chunk can be retrieved.
     */
    public $has_previous_chunk = false;
    /**
     * @var bool Indicates if the next Result Set Chunk can be retrieved.
     */
    public $has_next_chunk = false;
    /**
     * @var array An array of `Item Numbers` representing the contents of the current Result Set Chunk. `Item Numbers` start at **1**, up to the `$total_chunk_size`
     */
    public $chunk_contents = [];

    /**
     * Initialize a new Result Set Data Object
     * 
     * @param string $queryString The query string of the Result Set.
     * @param int $currentChunkSize The size of the current Result Set Chunk.
     * @param int $totalResultChunkSize The total number of items that are a part of the Result Set Chunk.
     * @return true Returns **true** on success.
     */
    public function __construct(string $queryString, int $currentChunkSize, int $totalResultChunkSize) {
      $this->max_chunk_size = (function () use ($queryString, $totalResultChunkSize) {
        $max = $totalResultChunkSize;
        $matches = [];

        if (preg_match('/(\bLIMIT\b)(?!.*\b\1\b) (\d+)/i', $queryString, $matches)) {
          $max = $matches[2];
        }

        return (int) $max;
      })();
      $this->current_chunk_size = $currentChunkSize;
      $this->total_chunk_size = $totalResultChunkSize;
      $this->current_chunk = (function () use ($queryString) {
        $chunk = 1;
        $matches = [];

        if ($this->max_chunk_size > 0 && preg_match('/(\bOFFSET\b)(?!.*\b\1\b) (\d+)/i', $queryString, $matches)) {
          $newChunk = ($matches[2] / $this->max_chunk_size) + 1;

          if ($newChunk > 0) {
            $chunk = $newChunk;
          }
        }

        return $chunk;
      })();
      $this->total_chunks = (int) ceil($totalResultChunkSize / $this->max_chunk_size);
      $this->has_previous_chunk = $this->current_chunk != 1;
      $this->has_next_chunk = $this->current_chunk != $this->total_chunks;
      $this->chunk_contents = (function () {
        $items = [];

        $lastItem = $this->max_chunk_size * $this->current_chunk;
        $firstItem = $lastItem - ($this->max_chunk_size - 1);

        for ($resultIndex = 0; $resultIndex < $this->current_chunk_size; $resultIndex++) {
          $items[] = $firstItem + $resultIndex;
        }

        return $items;
      })();

      return true;
    }
  };
?>