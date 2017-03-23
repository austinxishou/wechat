<?php

class MiniProgramTest extends TestCase
{
    protected function getMockMiniProgramAccessToken()
    {
        return [
            Mockery::mock('EasyWeChat\MiniProgram\AccessToken'),
            ['app_id' => 'simulation-app-id', 'secret' => 'simulation-app-secret']
        ];
    }

    /** @test */
    function get_session_key()
    {
        $user = Mockery::mock('EasyWeChat\MiniProgram\User\User[parseJSON]', $this->getMockMiniProgramAccessToken());
        $user->shouldReceive('parseJSON')->andReturnUsing(parseJsonSimulationClosure());
        $result = $user->getSessionKey('simulation-js-code');

        $this->assertEquals('simulation-js-code', $result['params']['js_code']);
    }


    /** @test */
    function create_qrcode()
    {
        $qrCode = Mockery::mock('EasyWeChat\MiniProgram\QRCode\QRCode[parseJSON]', $this->getMockMiniProgramAccessToken());
        $qrCode->shouldReceive('parseJSON')->andReturnUsing(parseJsonSimulationClosure());
        $result = $qrCode->create('pages/index');

        $this->assertEquals('pages/index', $result['params']['path']);
        $this->assertEquals(430, $result['params']['width']);
        $result = $qrCode->create('path/to/others', 555);
        $this->assertEquals(555, $result['params']['width']);
    }

    /** @test */
    function notice_send_and_test_without_enough_required_parameters()
    {
        $notice = Mockery::mock('EasyWeChat\MiniProgram\Notice\Notice[parseJSON]', $this->getMockMiniProgramAccessToken());
        $notice->shouldReceive('parseJSON')->andReturnUsing(parseJsonSimulationClosure());

        try {
            $notice->send(['something' => 'foo']);
        } catch (Exception $e) {
            $this->assertInstanceOf(EasyWeChat\Core\Exceptions\InvalidArgumentException::class, $e);
            $this->assertContains(' can not be empty!', $e->getMessage());
        }

        $result = $notice->send([
            'touser' => 'mingyoung',
            'template_id' => 'tid',
            'form_id' => 'fid'
        ]);

        $this->assertEquals('mingyoung', $result['params']['touser']);
        $this->assertEquals('tid', $result['params']['template_id']);
        $this->assertStringStartsWith(EasyWeChat\MiniProgram\Notice\Notice::API_SEND_NOTICE, $result['api']);
    }
}
