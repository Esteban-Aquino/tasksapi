<?php
class TaskException extends Exception
{

}

class Task
{
    private $_id;
    private $_title;
    private $_description;
    private $_deadline;
    private $_compleated;

    public function __construct($_id,$_title,$_description,$_deadline, $_compleated)
    {
        $this->setId($_id);
        $this->setTitle($_title);
        $this->setDescription($_description);
        $this->setDeadline($_deadline);
        $this->setCompleated($_compleated);
    }

    /**
     * Get the value of _id
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get the value of _title
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Get the value of _description
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Get the value of _deadline
     */
    public function getDeadline()
    {
        return $this->_deadline;
    }

    /**
     * Get the value of _compleated
     */
    public function getCompleated()
    {
        return $this->_compleated;
    }

    /**
     * Set the value of _id
     *
     * @return  self
     */
    public function setId($_id)
    {
        if (($_id !== null) && (!is_numeric($_id) || $_id <= 0 || $_id > 9223372036854775807 || $this->_id !== null)) {
            throw new TaskException("Task ID error");
        }
        $this->_id = $_id;

        return $this;
    }

    /**
     * Set the value of _title
     *
     * @return  self
     */
    public function setTitle($_title)
    {
        if(strlen($_title) < 0 || strlen($_title)>255) {
            throw new TaskException("Task title error");
        }
        $this->_title = $_title;

        return $this;
    }

    /**
     * Set the value of _description
     *
     * @return  self
     */
    public function setDescription($_description)
    {
        if(($_description !== null) && (strlen($_description)>16777215)) {
            throw new TaskException("Task descrioption error");
        }

        $this->_description = $_description;

        return $this;
    }

    /**
     * Set the value of _deadline
     *
     * @return  self
     */
    public function setDeadline($_deadline)
    {
        if (($_deadline !== null) && date_format(date_create_from_format('d/m/Y H:i', $_deadline),'d/m/Y H:i') != $_deadline ) {
            throw new TaskException("Task deadline date time error");
        }
        $this->_deadline = $_deadline;

        return $this;
    }

    /**
     * Set the value of _compleated
     *
     * @return  self
     */
    public function setCompleated($_compleated)
    {
        if (strtoupper($_compleated) !== 'Y' && strtoupper($_compleated) !== 'N') {
            throw new TaskException("Task completed must by Y or N");
        }
        $this->_compleated = $_compleated;

        return $this;
    }

    public function returnTaskAsArray() {
        $task = array();
        $task['id'] = $this->getId();
        $task['title'] = $this->getTitle();
        $task['description'] = $this->getDescription();
        $task['deadline'] = $this->getDeadline();
        $task['compleated'] = $this->getCompleated();
        return $task;
    }
}
