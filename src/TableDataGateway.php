<?php
/**
 * Copyright (c) 2009 Stefan Priebsch <stefan@priebsch.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Stefan Priebsch nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    MVC
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @license    BSD License
 */

namespace spriebsch\MVC;

/**
 * Table Data Gateway class.
 *
 * @author Stefan Priebsch <stefan@priebsch.de>
 * @copyright Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class TableDataGateway
{
	protected $db;
	protected $table;
	protected $dbTypes = array();
    protected $phpTypes = array();
	
	protected $insertStatement;
    protected $updateStatement;
	protected $deleteStatement;
    protected $selectOneStatement;
    protected $selectAllStatement;
    
    public function __construct(\PDO $db, $table, $dbTypes, $phpTypes)
	{
        $this->db       = $db;
        $this->table    = $table;
        $this->dbTypes  = $dbTypes;
        $this->phpTypes = $phpTypes;
	}
	
	/**
	 * Cast column values of result set to appropriate PHP types.
	 * 
	 * @param unknown_type $data
	 * @return unknown_type
	 */
	protected function typeCast($data)
	{
		foreach ($data as $key => &$value) {
			if (!isset($this->phpTypes[$key])) {
				continue;
			}

			switch ($this->phpTypes[$key]) {
				case 'bool':
					$value = (bool) $value;
                case 'float':
                    $value = (float) $value;
			}
		}

		return $data;
	}

	public function find($id)
	{
        if (!is_int($id)) {
            throw new DatabaseException('ID is not an integer');
        }
		
        if ($this->selectOneStatement === null) {
            $this->selectOneStatement = $this->db->prepare('SELECT * FROM ' . $this->table . ' WHERE id=:id;');
        }

        $this->selectOneStatement->bindValue(':id', $id, \PDO::PARAM_INT);
        $this->selectOneStatement->execute();
        
        if ($this->selectOneStatement->errorCode() != 0) {
            $message = $this->selectOneStatement->errorInfo();
            throw new DatabaseException('Select ID ' . $id . ' failed on table ' . $this->table . ': ' . $message[2]);
        }        

        $result = array_map(array($this, 'typeCast'), $this->selectOneStatement->fetchAll(\PDO::FETCH_ASSOC));
        return $result[0];
	}

    public function findAll()
    {
        if ($this->selectAllStatement === null) {
            $this->selectAllStatement = $this->db->prepare('SELECT * FROM ' . $this->table);
        }
        
        $this->selectAllStatement->execute();
        
        if ($this->selectAllStatement->errorCode() != 0) {
            $message = $this->selectAllStatement->errorInfo();
            throw new DatabaseException('Select all failed on table ' . $this->table . ': ' . $message[2]);
        }        

        return array_map(array($this, 'typeCast'), $this->selectAllStatement->fetchAll(\PDO::FETCH_ASSOC));        
    }

    /**
     * Update row in table.
     * Returns the number of updated rows.
     *
     * @param array $record
     * @return array
     */
	public function update(array $record)
	{
        // Make sure that $record contains all columns.
        if (sizeof($record) != sizeof($this->dbTypes)) {
            throw new DatabaseException('Record does not contain all columns of ' . $this->table . ' table');
        }
        
        if (!isset($record['id'])) {
            throw new DatabaseException('Record has no ID');
        }

        if ($this->updateStatement === null) {

            $sql = 'UPDATE ' . $this->table . ' SET ';
            $fields = array();

            $data = $record;
            unset($data['id']);
            
            foreach (array_keys($data) as $key) {
                $fields[] = $key . '=:' . $key;           
            }
        
            $sql .= implode(',', $fields) . ' WHERE id=:id;';

            $this->updateStatement = $this->db->prepare($sql);
        }

        foreach ($record as $key => $value) {
            $this->updateStatement->bindValue(':' . $key, $value, $this->dbTypes[$key]);
        }

        $this->updateStatement->execute();

        if ($this->updateStatement->errorCode() != 0) {
            $message = $this->updateStatement->errorInfo();
            throw new DatabaseException('Update ID ' . $data['id'] . ' failed on table ' . $this->table . ': ' . $message[2]);
        }        

        return $this->updateStatement->rowCount();        
	}

	/**
	 * Insert new row into table.
     * Returns the number of inserted rows.
	 *
	 * @param array $record
	 * @return array
	 */
	public function insert(array $record)
	{
		// Make sure that $record contains all columns but the id one.
		if (sizeof($record) != sizeof($this->dbTypes) - 1) {
            throw new DatabaseException('Record does not contain all columns of ' . $this->table . ' table');
		}
		
        if ($this->insertStatement === null) {

        	$sql = 'INSERT INTO ' . $this->table . ' (' . implode(',', array_keys($record)) . ') VALUES (';
        	$placeholders = array();

        	foreach (array_keys($record) as $key) {
                $placeholders[] = ':' . $key;        	
            }
        
            $sql .= implode(',', $placeholders) . ');';
            
            $this->insertStatement = $this->db->prepare($sql);
        }
        
        foreach ($record as $key => $value) {
            $this->insertStatement->bindValue(':' . $key, $value, $this->dbTypes[$key]);
        }

        $this->insertStatement->execute();
        
        if ($this->insertStatement->errorCode() != 0) {
        	$message = $this->insertStatement->errorInfo();
        	throw new DatabaseException('Insert failed on table ' . $this->table . ': ' . $message[2]);
        }        

        return $this->insertStatement->rowCount();        
	}

	/**
	 * Delete a record from the table.
	 * Returns the number of deleted rows.
	 * 
	 * @param int $id
	 * @return int
	 */
	public function delete($id)
	{
		if (!is_int($id)) {
			throw new DatabaseException('ID is not an integer');
		}

        if ($this->deleteStatement === null) {
            $this->deleteStatement = $this->db->prepare('DELETE FROM ' . $this->table . ' WHERE id=:id;');
        }
        
        $this->deleteStatement->bindValue(':id', $id, \PDO::PARAM_INT);
        $this->deleteStatement->execute();
        
        if ($this->deleteStatement->errorCode() != 0) {
            $message = $this->deleteStatement->errorInfo();
            throw new DatabaseException('Delete ID ' . $id . ' failed on table ' . $this->table . ': ' . $message[2]);
        }        

        return $this->deleteStatement->rowCount();        
	}
}
?>