<?php
/**
 * Created by PhpStorm.
 * User: thilan
 * Date: 10/15/18
 * Time: 10:43 PM
 */

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Laravel\Passport\Http\Controllers\AccessTokenController as ATController;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Response;

class AccessTokenController extends ATController
{

    public function issueToken(ServerRequestInterface $request)
    {
        try {
            $username = $request->getParsedBody()['username'];

            //get user
            //change to 'email' if you want
            $user = User::where('email', '=', $username)->first();

            //generate token
            $tokenResponse = parent::issueToken($request);

            //convert response to json string
            $content = $tokenResponse->getContent();

            //convert json to array
            $data = json_decode($content, true);

            if ($user && !$user->verified) {
                throw new OAuthServerException('The user not verified.', 6, 'invalid_credentials', 401);
            }

            if (isset($data["error"]))
                throw new OAuthServerException('The user credentials were incorrect.', 6, 'invalid_credentials', 401);

            $respondData = [
                'access_token' => $data['access_token'],
                'token_type' => $data['token_type'],
                'expires_in' => $data['expires_in'],
                'refresh_token' => $data['refresh_token'],
            ];
            $user = collect($user);
            $user->put('access_token', $data['access_token']);
            $user->put('access_token', $data['token_type']);
            $user->put('access_token', $data['expires_in']);
            $user->put('access_token', $data['refresh_token']);
            return Response::json($respondData);
        } catch (ModelNotFoundException $e) {
            return response(["message" => "Account is not found"], 500);
        } catch (OAuthServerException $e) {
            return response(["message" => $e->getMessage()], 500);
        } catch (Exception $e) {
            return response(["message" => "Internal server error"], 500);
        }
    }
}