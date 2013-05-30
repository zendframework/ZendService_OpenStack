<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendService\OpenStack;

/**
 * Identity OpenStack API
 *
 * Note: This OpenStack API is still in DRAFT
 *
 * @see http://docs.openstack.org/api/openstack-identity-service/2.0/content/index.html
 */
class Identity extends AbstractOpenStack
{
    const API_VERSION          = '2.0';

    const ERROR_PARAM_USERNAME = 'You must specify a valid username';
    const ERROR_PARAM_TOKEN    = 'You must specify a valid token';
    const ERROR_PARAM_USER     = 'The user parameter must be a not empty array';
    const ERROR_PARAM_USER_ID  = 'You must specify the user ID';
    const ERROR_PARAM_TENANT   = 'The tenant parameter must be a not empty array';

    /**
     * Authenticate using username and password
     *
     * @param  string $name
     * @param  string $password
     * @param  string $tenantName
     * @return bool|array
     * @throws Exception\InvalidArgumentException
     */
    public function authenticate($name, $password, $tenantName = null)
    {
        if (empty($name)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_USERNAME);
        }
        if (empty($password)) {
            throw new Exception\InvalidArgumentException(
                'The password of the user cannot be empty'
            );
        }
        return $this->api->authenticate($name, $password, $tenantName);
    }

    /**
     * Authenticate using the username and the API's key
     *
     * @param  string $name
     * @param  string $key
     * @return bool|array
     * @throws Exception\InvalidArgumentException
     */
    public function authenticateByKey($name, $key)
    {
        if (empty($name)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_USERNAME);
        }
        if (empty($key)) {
            throw new Exception\InvalidArgumentException(
                'The API key cannot be empty'
            );
        }
        return $this->api->authenticateByKey($name, $key);
    }

    /**
     * Validate token
     *
     * @param  string $token
     * @param  string $belongsTo
     * @return bool|array
     * @throws Exception\InvalidArgumentException
     */
    public function validateToken($token, $belongsTo = null)
    {
        if (empty($token)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_TOKEN);
        }
        if (!empty($belongsTo)) {
            $this->api->setQueryParams(array( 'belongsTo' => $belongsTo ));
        }
        $result = $this->api->validateToken($token);
        if (!empty($belongsTo)) {
            $this->api->setQueryParams();
        }
        return $result;
    }

    /**
     * Check token
     *
     * @param  string $token
     * @param  string $belongsTo
     * @return bool|array
     * @throws Exception\InvalidArgumentException
     */
    public function checkToken($token, $belongsTo = null)
    {
        if (empty($token)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_TOKEN);
        }
        if (!empty($belongsTo)) {
            $this->api->setQueryParams(array( 'belongsTo' => $belongsTo ));
        }
        $result = $this->api->checkToken($token);
        if (!empty($belongsTo)) {
            $this->api->setQueryParams();
        }
        return $result;
    }

    /**
     * List endpoints token
     *
     * @param  string $token
     * @param  string $belongsTo
     * @return bool|array
     * @throws Exception\InvalidArgumentException
     */
    public function listEndpointsToken($token, $belongsTo = null)
    {
        if (empty($token)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_TOKEN);
        }
        if (!empty($belongsTo)) {
            $this->api->setQueryParams(array( 'belongsTo' => $belongsTo ));
        }
        $result = $this->api->listEndpointsToken($token);
        if (!empty($belongsTo)) {
            $this->api->setQueryParams();
        }
        return $result;
    }

    /**
     * List users
     *
     * @return bool|array
     */
    public function listUsers()
    {
        return $this->api->listUsers();
    }

    /**
     * Add a user
     *
     * The user is specified with this array (
     *  'username' => '',
     *  'email'    => '',
     *  'password' => ''
     * )
     *
     * @param  array $user
     * @return type
     * @throws Exception\InvalidArgumentException
     */
    public function addUser(array $user)
    {
        if (empty($user)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_USER);
        }
        if (!isset($user['password']) || !isset($user['email']) || !isset($user['username'])) {
            throw new Exception\InvalidArgumentException(
                'The user array must contains username, email and password keys'
            );
        }
        return $this->api->addUser($user);
    }

    /**
     * Update a user
     *
     * The user is specified with this array(
     *     'id'       => '',
     *     'username' => '',
     *     'email'    => '',
     * )
     * @param  array $user
     * @return bool|array
     * @throws Exception\InvalidArgumentException
     */
    public function updateUser(array $user)
    {
        if (empty($user)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_USER);
        }
        if (!isset($user['id']) || !isset($user['email']) || !isset($user['username'])) {
            throw new Exception\InvalidArgumentException(
                'The user array must contains id, username, email'
            );
        }
        return $this->api->updateUser($user);
    }

    /**
     * Delete a user
     *
     * @param  string $id
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function deleteUser($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_USER_ID);
        }
        $this->api->deleteUser($id);
        return $this->api->isSuccess();
    }

    /**
     * List of global roles by user ID
     *
     * @param  string $id
     * @param  string $serviceId
     * @return bool|array
     * @throws Exception\InvalidArgumentException
     */
    public function listGlobalRoles($id, $serviceId = null)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_USER_ID);
        }
        if (!empty($serviceId)) {
            $this->api->setQueryParams(array( 'serviceId' => $serviceId ));
        }
        $result = $this->api->listGlobalRoles($id);
        if (!empty($serviceId)) {
            $this->api->setQueryParams();
        }
        return $result;
    }

    /**
     * Add a tenant
     *
     * A tenant is specified with this array(
     *     'name'        => '',
     *     'description' => '',
     * )
     *
     * @param  array $tenant
     * @return bool|array
     * @throws Exception\InvalidArgumentException
     */
    public function addTenant(array $tenant)
    {
        if (empty($tenant)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_TENANT);
        }
        if (!isset($tenant['name']) || !isset($tenant['description'])) {
            throw new Exception\InvalidArgumentException(
                'The tenant array must contains name and description'
            );
        }
        return $this->api->addTenant($tenant);
    }

    /**
     * Update a tenant
     *
     * A tenant is specified with this array(
     *     'id'          => '',
     *     'name'        => '',
     *     'description' => '',
     * )
     *
     * @param  array $tenant
     * @return bool|array
     * @throws Exception\InvalidArgumentException
     */
    public function updateTenant(array $tenant)
    {
        if (empty($tenant)) {
            throw new Exception\InvalidArgumentException(self::ERROR_PARAM_TENANT);
        }
        if (!isset($tenant['id']) || !isset($tenant['name']) || !isset($tenant['description'])) {
            throw new Exception\InvalidArgumentException(
                'The tenant array must contains id, name and description'
            );
        }
        return $this->api->updateTenant($tenant);
    }

    /**
     * Delete a tenant
     *
     * @param  string $id
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function deleteTenant($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException('You must specify the tenant ID');
        }
        $this->api->deleteTenant($tenant);
        return $this->api->isSuccess();
    }
}
