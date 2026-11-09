# Port acik mi diye bakar. Aciksa 0, kapaliysa 1 dondurur.
# 127.0.0.1 ve ::1 ikisini de dener. Cevap gelmezse yarim saniye bekler.
param([int]$Port)

function Port-Acik {
    param([string]$Adres, [System.Net.Sockets.AddressFamily]$Aile)
    $istemci = New-Object System.Net.Sockets.TcpClient($Aile)
    try {
        $bekle = $istemci.BeginConnect($Adres, $Port, $null, $null)
        $geldi = $bekle.AsyncWaitHandle.WaitOne(500)
        if (-not $geldi) {
            return $false
        }
        $istemci.EndConnect($bekle)
        return $istemci.Connected
    } catch {
        return $false
    } finally {
        $istemci.Close()
    }
}

$acik = (Port-Acik "127.0.0.1" ([System.Net.Sockets.AddressFamily]::InterNetwork)) -or (Port-Acik "::1" ([System.Net.Sockets.AddressFamily]::InterNetworkV6))
if ($acik) {
    exit 0
}
exit 1
