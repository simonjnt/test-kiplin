### Implementation of the Kiplin backend test

I decided to implement the todo-list storage with PostgreSQL in a relational database. The reason is that PostgreSQL is one of the most powerful database management systems, is very convenient to use, and is the system I have the most experience with.
Moreover, a relational database is a go-to choice in terms of security and efficiency of dynamic data storage.

In order to run the database properly, I used the already implemented in the project Docker, and added a new PostgreSQL image into the `docker-compose.yml`, then mounted an initialization script (`init.sql`) to create the `todo` table on the docker image initializaiton.
I created the `DatabaseTodoRepository.php` as the main database-driven repository.

The existing implementation of the Todo class makes me need to store the data as an array. One of the best way to store an array in PostgreSQL is to make the column as JSON type. As I don't have Doctrine implemented, I need to do myself the `json_encode`/`json_decode` switch, but the benefit of storing in JSON easily compensates the memory taken by the `json_encode`/`json_decode`.
Combined with the id column which I can make as UUID type, I have 2 columns for my todo table.

Then, I connect my database and my repository by adding a private function `executeQuery`, to avoid code duplication. I used this private function in the three others, to retrieve or insert the data I want.
After implementing the `get`, `save`, and `opened` functions, I added my new class into the test cases to check it.

### Improvements and suggestions

On a bigger project, I would have used an database management bundle or ORM such as Doctrine, in order to improve code stability, cleanness, and more importantly security.
In this project, security is not the bigger concern as a to-do list does not contain critical data such as bank account or personal information. The bigger security concern we need to check is the SQL injection to avoid malevolent data manipulation, and that's why all the data passed to the SQL query are binded with the `pg_query_params` function.
Moreover, this project is very small and all the database-related code lies in one small file. Configuring Doctrine or any other ORM would have been very overkill as the setup takes more times than development itself, and the code will not evolve in the future.
For all these reasons, I chose to implement native-PHP database management.

In order to improve code efficiency and to optimize and shorten it, I would have created the Todo classes with all separate attributes for the contents. So in the database, we could have separate `id`, `status` and `description` columns. With the most recent versions of PHP (>= 8.1), the enumerations and the typed attributes, one simple `Todo` class just as follows would have been enough :

```php
// This Enum shortens the code a lot more and makes it clearer, for the same functionalities and even more capacity of evolving
enum Status: string
{
    case OPENED = 'opened';
    case CLOSED = 'closed';
}

class Todo
{
    // The unique UUID generation is directly made into the database and no longer lies in the code
    private string $id;
    private string $description;
    private Status $status;

    public function getId(): string
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }
}
```

With that kind of implementation, the code would have been better optimized, with a better consistency and very much easier to understand.
As an example, I could have integrated directly the `status="opened"` check into the SQL request as the data would have been saved straight like that, and saved many useless PHP iterations to check the status afterward. It's always better to filter data in SQL than in PHP, because SQL if specially made for that.