<?php
/**
 * Project: zaralab
 * Filename: Database.php
 *
 * @author Miroslav Yovchev <m.yovchev@corllete.com>
 * @since 08.11.15
 */

/**
 * Database Context trait
 */
trait DatabaseTrait
{

    /**
     * @BeforeSuite
     */
    public static function databaseSetup()
    {
        $base = realpath(__DIR__.'/../..');
        if(self::PRINT_HOOKS) print("--- create test database\n");
        self::createDatabase($base);
    }


    /**
     * @AfterSuite
     */
    public static function databaseShutdown()
    {
        $base = realpath(__DIR__.'/../..');
        if(self::PRINT_HOOKS) print("--- drop test database\n");
        self::dropDatabase($base);
    }

    /**
     * @BeforeFeature @fixtures
     */
    public static function fixturesSetup()
    {
        $base = realpath(__DIR__.'/../..');
        if(self::PRINT_HOOKS) print("--- load fixtures\n");
        self::loadFixtures($base);
    }


    /**
     * @AfterFeature @fixtures
     */
    public static function fixturesShutdown()
    {
        $base = realpath(__DIR__.'/../..');
        if(self::PRINT_HOOKS) print("--- purge fixtures\n");
        self::removeFixtures($base);
    }

    protected static function createDatabase($base)
    {
        exec($base.'/app/console database:drop --env=test');
        exec($base.'/app/console database:create --env=test', $out, $code);

        if ((int) $code !== 0) {
            print(implode("\n", $out));

            throw new \Exception(
                sprintf("Can not create test database - exit code %d", (int) $code)
            );
        }

        exec($base.'/app/console migrations:migrate --no-interaction --env=test', $out, $code);

        if ((int) $code !== 0) {
            print(implode("\n", $out));

            throw new \Exception(
                sprintf("Can not create test database - exit code %d", (int) $code)
            );
        }
    }

    protected static function dropDatabase($base)
    {
        exec($base.'/app/console database:drop --env=test', $out, $code);

        if ((int) $code !== 0) {
            print(implode("\n", $out));

            throw new \Exception(
                sprintf("Can not drop test database - exit code %d", (int) $code)
            );
        }
    }

    protected static function loadFixtures($base)
    {
        exec($base.'/app/console fixtures:load --truncate --no-interaction --env=test', $out, $code);
        if ((int) $code !== 0) {
            print(implode("\n", $out));

            throw new \Exception(
                sprintf("Can not load database data fixtures - exit code %d", (int) $code)
            );
        }
    }

    protected static function removeFixtures($base)
    {
        exec($base.'/app/console fixtures:load --truncate-only --no-interaction --env=test', $out, $code);
        if ((int) $code !== 0) {
            print(implode("\n", $out));

            throw new \Exception(
                sprintf("Can not purge data fixtures - exit code %d", (int) $code)
            );
        }
    }
}