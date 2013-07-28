<?php

/**
 * @author Cedric LOMBARDOT <cedric.lombardot@gmail.com>
 */
class VisibilityBehaviorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Post')) {
            $schema = <<<EOF
<database name="visibility_behavior" defaultIdMethod="native">
    <table name="post">
        <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />

        <column name="title" type="varchar" size="255" /> 

        <behavior name="visibility">
            <parameter name="visibilities" value="me, friends, all" />
            <parameter name="default_visibility" value="me" />
            <!-- Hiearchy of visibilities -->
            <parameter name="hierarchy" value="when it's visible for friends it's visible for me" />
            <parameter name="hierarchy" value="when it's visible for all it's visible for friends" />
            <parameter name="hierarchy" value="when it's visible for all it's visible for me" />
            <!-- Choose columns to apply -->
            <parameter name="apply_to" value="title" />
            <parameter name="with_description" value="true" />
        </behavior>
    </table>
</database>
EOF;
            $builder = new PropelQuickBuilder();
            $config  = $builder->getConfig();
            $config->setBuildProperty('behavior.visibility.class', '../src/VisibilityBehavior');
            $builder->setConfig($config);
            $builder->setSchema($schema);

            $builder->build();
        }
    }

    public function testObjectMethods()
    {
        $this->assertTrue(method_exists('Post', 'isTitleVisibleForMe'));
        $this->assertTrue(method_exists('Post', 'isTitleVisibleForFriends'));
        $this->assertTrue(method_exists('Post', 'isTitleVisibleForAll'));

        $this->assertTrue(method_exists('Post', 'copyAsVisibleForMe'));
        $this->assertTrue(method_exists('Post', 'copyAsVisibleForFriends'));
        $this->assertTrue(method_exists('Post', 'copyAsVisibleForAll'));

        $this->assertTrue(method_exists('Post', 'getTitleVisibility'));
        $this->assertTrue(method_exists('Post', 'setTitleVisibility'));

        $this->assertTrue(defined('Post::VISIBILITY_ME'));
        $this->assertTrue(defined('Post::VISIBILITY_FRIENDS'));
        $this->assertTrue(defined('Post::VISIBILITY_ALL'));

        $this->assertTrue(defined('Post::VISIBILITY_NORMALIZED_ME'));
        $this->assertTrue(defined('Post::VISIBILITY_NORMALIZED_FRIENDS'));
        $this->assertTrue(defined('Post::VISIBILITY_NORMALIZED_ALL'));

        $this->assertEquals('me', Post::VISIBILITY_NORMALIZED_ME);
    }

    public function testDefaultVisibility()
    {
        $post = new Post();
        $this->assertTrue($post->isTitleVisibleForMe());
        $this->assertFalse($post->isTitleVisibleForFriends());
        $this->assertFalse($post->isTitleVisibleForAll());
    }

    public function testGetNormalizedVisibilities()
    {
        $expected = array(
            Post::VISIBILITY_NORMALIZED_ME,
            Post::VISIBILITY_NORMALIZED_FRIENDS,
            Post::VISIBILITY_NORMALIZED_ALL,
        );

        $this->assertCount(3, Post::getNormalizedVisibilities());
        $this->assertEquals($expected, Post::getNormalizedVisibilities());
    }

    public function testGetAvailableVisibilities()
    {
        $post = new Post();
        $expected = array(
            Post::VISIBILITY_ME,
            Post::VISIBILITY_FRIENDS,
            Post::VISIBILITY_ALL,
        );

        $this->assertEquals($expected, $post->getAvailableVisibilities());
    }
}
