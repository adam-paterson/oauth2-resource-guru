<?php

namespace AdamPaterson\OAuth2\Client\Test\Provider;

use AdamPaterson\OAuth2\Client\Provider\ResourceGuru;
use Mockery as m;
use ReflectionClass;

class ResourceGuruTest extends \PHPUnit_Framework_TestCase
{
    protected $provider;

    protected static function getMethod($name)
    {
        $class = new ReflectionClass('AdamPaterson\OAuth2\Client\Provider\Imgur');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    protected function setUp()
    {
        $this->provider = new ResourceGuru([
            'clientId'      => 'mock_client_id',
            'clientSecret'  => 'mock_secret',
            'redirectUri'   => 'none',
        ]);
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }


    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);
        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());
    }

    public function testGetAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        $this->assertEquals('/oauth/authorize', $uri['path']);
    }

    public function testGetBaseAccessTokenUrl()
    {
        $params = [];
        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);
        $this->assertEquals('/oauth/token', $uri['path']);
    }

    public function testGetAccessToken()
    {
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn('{"access_token":"mock_access_token", "token_type":"bearer"}');
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertNull($token->getExpires());
        $this->assertNull($token->getRefreshToken());
        $this->assertNull($token->getResourceOwnerId());
    }

    public function testUserData()
    {
        $id = rand(1, 1000);
        $firstName = uniqid();
        $lastName = uniqid();
        $email = uniqid();
        $image = uniqid();
        $timeZone = uniqid();
        $lastLoginAt = uniqid();
        $lastLogoutAt = uniqid();
        $lastActivityAt = uniqid();
        $activationState = uniqid();
        $createdAt = uniqid();
        $updatedAt = uniqid();

        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $postResponse->shouldReceive('getBody')->andReturn('access_token=mock_access_token&expires=3600&refresh_token=mock_refresh_token');
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'application/x-www-form-urlencoded']);
        $postResponse->shouldReceive('getStatusCode')->andReturn(200);
        $userResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $userResponse->shouldReceive('getBody')->andReturn('{"id": '.$id.',"first_name": "'.$firstName.'","last_name": "'.$lastName.'","email": "'.$email.'","image": "'.$image.'","timezone": "'.$timeZone.'","last_login_at": "'.$lastLoginAt.'","last_logout_at": "'.$lastLogoutAt.'","last_activity_at": "'.$lastActivityAt.'","activation_state": "'.$activationState.'","created_at": "'.$createdAt.'","updated_at": "'.$updatedAt.'"}');

        $userResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $userResponse->shouldReceive('getStatusCode')->andReturn(200);
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')
            ->times(2)
            ->andReturn($postResponse, $userResponse);
        $this->provider->setHttpClient($client);
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $user = $this->provider->getResourceOwner($token);

        $this->assertEquals($id, $user->getId());
        $this->assertEquals($id, $user->toArray()['id']);
        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($firstName, $user->toArray()['first_name']);
        $this->assertEquals($lastName, $user->getLastName());
        $this->assertEquals($lastName, $user->toArray()['last_name']);
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($email, $user->toArray()['email']);
        $this->assertEquals($image, $user->getImage());
        $this->assertEquals($image, $user->toArray()['image']);
        $this->assertEquals($timeZone, $user->getTimezone());
        $this->assertEquals($timeZone, $user->toArray()['timezone']);
        $this->assertEquals($lastLoginAt, $user->getLastLoginAt());
        $this->assertEquals($lastLoginAt, $user->toArray()['last_login_at']);
        $this->assertEquals($lastLogoutAt, $user->getLastLogoutAt());
        $this->assertEquals($lastLogoutAt, $user->toArray()['last_logout_at']);
        $this->assertEquals($lastActivityAt, $user->getLastActivityAt());
        $this->assertEquals($lastActivityAt, $user->toArray()['last_activity_at']);
        $this->assertEquals($activationState, $user->getActivationState());
        $this->assertEquals($activationState, $user->toArray()['activation_state']);
        $this->assertEquals($createdAt, $user->getCreatedAt());
        $this->assertEquals($createdAt, $user->toArray()['created_at']);
        $this->assertEquals($updatedAt, $user->getUpdatedAt());
        $this->assertEquals($updatedAt, $user->toArray()['updated_at']);

    }

    /**
     * @expectedException League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function testExceptionThrownWhenErrorObjectReceived()
    {
        $message = uniqid();
        $status = rand(400,600);
        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $postResponse->shouldReceive('getBody')->andReturn(' {"error_description":"'.$message.'"}');
        $postResponse->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $postResponse->shouldReceive('getStatusCode')->andReturn($status);
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')
            ->times(1)
            ->andReturn($postResponse);
        $this->provider->setHttpClient($client);
        $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }
}
