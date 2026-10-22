VERSION 5.00
Object = "{F9043C88-F6F2-101A-A3C9-08002B2F49FB}#1.2#0"; "comdlg32.ocx"
Begin VB.Form formMain 
   BorderStyle     =   1  'Fixed Single
   Caption         =   "GrFinger - Sample application - Visual Basic 6.0"
   ClientHeight    =   7755
   ClientLeft      =   3735
   ClientTop       =   1455
   ClientWidth     =   7560
   Icon            =   "formMain.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   ScaleHeight     =   517
   ScaleMode       =   3  'Pixel
   ScaleWidth      =   504
   StartUpPosition =   2  'CenterScreen
   Begin VB.CheckBox ckBoxAutoExtract 
      Caption         =   "Auto Extract"
      Height          =   255
      Left            =   6120
      TabIndex        =   8
      Top             =   6240
      Value           =   1  'Checked
      Width           =   1335
   End
   Begin VB.CommandButton btExtract 
      Caption         =   "Extract template"
      Enabled         =   0   'False
      Height          =   375
      Left            =   6120
      TabIndex        =   7
      Top             =   4200
      Visible         =   0   'False
      Width           =   1335
   End
   Begin VB.ListBox lbLog 
      Height          =   7470
      Left            =   120
      TabIndex        =   6
      Top             =   120
      Width           =   5895
   End
   Begin VB.Timer tmrImage 
      Interval        =   100
      Left            =   6240
      Top             =   2160
   End
   Begin VB.Timer tmrFinger 
      Interval        =   100
      Left            =   6240
      Top             =   1680
   End
   Begin VB.Timer tmrStatus 
      Interval        =   100
      Left            =   6240
      Top             =   1080
   End
   Begin MSComDlg.CommonDialog CommonDialog 
      Left            =   6240
      Top             =   480
      _ExtentX        =   847
      _ExtentY        =   847
      _Version        =   393216
      Filter          =   "Bitmap (*.bmp)|*.bmp"
   End
   Begin VB.CommandButton btClearDB 
      Caption         =   "Clear database"
      Height          =   375
      Left            =   6120
      TabIndex        =   3
      Top             =   4920
      Visible         =   0   'False
      Width           =   1335
   End
   Begin VB.CommandButton btClearLog 
      Caption         =   "Vaciar Log."
      Height          =   375
      Left            =   6120
      TabIndex        =   4
      Top             =   5400
      Width           =   1335
   End
   Begin VB.CommandButton btVerify 
      Caption         =   "Verificar"
      Enabled         =   0   'False
      Height          =   375
      Left            =   6120
      TabIndex        =   1
      Top             =   3240
      Width           =   1335
   End
   Begin VB.CommandButton btIdentify 
      Caption         =   "Identificar"
      Enabled         =   0   'False
      Height          =   375
      Left            =   6120
      TabIndex        =   2
      Top             =   3720
      Width           =   1335
   End
   Begin VB.CommandButton btEnroll 
      Caption         =   "Registrar"
      Enabled         =   0   'False
      Height          =   375
      Left            =   6120
      TabIndex        =   0
      Top             =   2760
      Width           =   1335
   End
   Begin VB.CheckBox ckBoxAutoIdentify 
      Caption         =   "Auto identify"
      Height          =   315
      Left            =   6120
      TabIndex        =   5
      Top             =   5880
      Value           =   1  'Checked
      Visible         =   0   'False
      Width           =   1335
   End
   Begin VB.Image img 
      BorderStyle     =   1  'Fixed Single
      Height          =   255
      Left            =   7200
      Stretch         =   -1  'True
      Top             =   120
      Visible         =   0   'False
      Width           =   255
   End
   Begin VB.Menu menuImage 
      Caption         =   "&Image"
      Begin VB.Menu menuImgSave 
         Caption         =   "&Save..."
         Shortcut        =   ^S
      End
      Begin VB.Menu menuImgFromFile 
         Caption         =   "&Load From File..."
         Shortcut        =   ^L
      End
   End
   Begin VB.Menu menuOptions 
      Caption         =   "&Options..."
   End
   Begin VB.Menu menuVersion 
      Caption         =   "&Version"
   End
End
Attribute VB_Name = "formMain"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
'-------------------------------------------------------------------------------
'GrFinger Sample
'(c) 2005 Griaule Tecnologia Ltda.
'http://www.griaule.com
'-------------------------------------------------------------------------------
'
'This sample is provided with "GrFinger Fingerprint Recognition Library" and
'can't run without it. It's provided just as an example of using GrFinger
'Fingerprint Recognition Library and should not be used as basis for any
'commercial product.
'
'Griaule Tecnologia makes no representations concerning either the merchantability
'of this software or the suitability of this sample for any particular purpose.
'
'THIS SAMPLE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR
'IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
'OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
'IN NO EVENT SHALL GRIAULE BE LIABLE FOR ANY DIRECT, INDIRECT,
'INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
'NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
'DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
'THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
'(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
'THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
'
'You can download the free version of GrFinger directly from Griaule website.
'
'These notices must be retained in any copies of any part of this
'documentation and/or sample.
'
'-------------------------------------------------------------------------------

' -----------------------------------------------------------------------------------
' GUI routines: main form
' -----------------------------------------------------------------------------------

Option Explicit


' Application startup code
Private Sub Form_Load()
    Dim err As Integer
    
    ' Initialize GrFinger Library
    err = InitializeGrFinger()
    ' Print result in log
    If err < 0 Then
        writeError (err)
        Exit Sub
    Else
        writeLog ("**GrFinger Inciado Correctamente**")
    End If
End Sub

' Application finalization code
Private Sub Form_Terminate()
    ' disable timers
    tmrFinger.Enabled = False
    tmrImage.Enabled = False
    tmrStatus.Enabled = False
    ' wait all events to be consumed
    While fireFinger Or fireStatus Or fireImage
    Wend
    ' finalize GrFinger
    Call FinalizeGrFinger
End Sub

' Add a fingerprint to database
Private Sub btEnroll_Click()
    Dim id As Integer
    
    ' add fingerprint
    id = Enroll
    ' write result to log
    If id >= 0 Then
        writeLog ("Huella registrada con la id = " & id)
    Else
        writeLog ("Error: Huella no registrada")
    End If
End Sub

' Identify a fingerprint
Public Sub btIdentify_Click()
    Dim ret As Integer, score As Long
    
    score = 0
    ' identify it
    ret = Identify(score)
    ' write result to log
    If ret > 0 Then
        writeLog ("Huella identificada con ID = " & ret & ". Grado de similitud = " & score & ".")
        Call PrintBiometricDisplay(True, GR_DEFAULT_CONTEXT)
    ElseIf ret = 0 Then
        writeLog ("Huella no reconocida.")
    Else
        writeError (ret)
    End If
End Sub

' Check a fingerprint
Private Sub btVerify_Click()
    Dim id As Integer
    Dim ret As Integer
    Dim score As Long
    Dim sid As String

    ' ask target fingerprint ID
    score = 0
    sid = InputBox("Introduzca la ID para verificar huella ", "Verify", "")
    If sid <> "" Then
        ' compare fingerprints
        ret = Verify(Val(sid), score)
        ' write result to log
        If ret < 0 Then
            writeError (ret)
        ElseIf ret = GR_NOT_MATCH Then
            writeLog ("La huella no corresponde a la ID, grado de similitud: " & score)
        Else
            writeLog ("La huella corresponde a la ID, grado de similitud: " & score)
            ' if they match, display matching minutiae/segments/directions
            Call PrintBiometricDisplay(True, GR_DEFAULT_CONTEXT)
        End If
    End If
End Sub

' Extract a template from a fingerprint image
Public Sub btExtract_Click()
    Dim ret As Integer

    ' extract template
    ret = ExtractTemplate()
    ' write template quality to log
    If ret = GR_BAD_QUALITY Then
        writeLog ("Huella leida corretamente, calidad baja.")
    ElseIf ret = GR_MEDIUM_QUALITY Then
        writeLog ("Huella leida corretamente, calidad media")
    ElseIf ret = GR_HIGH_QUALITY Then
        writeLog ("Huella leida corretamente, calidad alta")
    End If
    If ret >= 0 Then
        ' if no error, display minutiae/segments/directions into the image
        Call PrintBiometricDisplay(True, GR_NO_CONTEXT)
        ' enable operations we can do over extracted template
        btExtract.Enabled = False
        btEnroll.Enabled = True
        btIdentify.Enabled = True
        btVerify.Enabled = True
    Else
        ' write error to log
        writeError (ret)
    End If
End Sub

' Clear database
Private Sub btClearDB_Click()
    ' clear database
    DB.clearDB
    ' write result to log
    writeLog ("Base de datos vaciada...")
End Sub

' Clear log
Private Sub btClearLog_Click()
    lbLog.Clear
End Sub

' Save fingerprint image to a file
Private Sub menuImgSave_Click()
    ' we need an image
    If raw.height < 1 Or raw.width < 1 Then
        MsgBox "No hay imágenes para guardar."
        Exit Sub
    End If
        
    ' open "save" dialog
    CommonDialog.Filter = "BMP files (*.bmp)|*.bmp|All files (*.*)|*.*"
    CommonDialog.FilterIndex = 1
    CommonDialog.ShowSave
    
    ' Save image.
    If Not CommonDialog.CancelError And CommonDialog.filename <> "" Then
        If GrCapSaveRawImageToFile(raw.img(0), raw.width, raw.height, CommonDialog.filename, GRCAP_IMAGE_FORMAT_BMP) <> GR_OK Then
            writeLog ("Error al guardar la imagen.")
        End If
    End If
End Sub

' Load a fingerprint image from a file
Private Sub menuImgFromFile_Click()
    ' open "load" dialog
    CommonDialog.Filter = "BMP files (*.bmp)|*.bmp|All files (*.*)|*.*"
    CommonDialog.FilterIndex = 1
    CommonDialog.ShowOpen
    
    ' load image
    If Not CommonDialog.CancelError And CommonDialog.filename <> "" Then
       Dim res As Long
       ' Getting resolution.
        res = Val(InputBox("Introduzca la resolución de la imagen seleccionada.", "Resolución"))
        ' Checking if action was canceled, no value or an invalid value was entered.
        If res <> 0 Then
            If GrCapLoadImageFromFile(CommonDialog.filename, res) <> GR_OK Then
                writeLog ("Error al cargar la imagen.")
            End If
        End If
    End If
End Sub

' Open "Options" window
Private Sub menuOptions_Click()
    Dim ret As Integer
    Dim thresholdId As Long
    Dim rotationMaxId As Long
    Dim thresholdVr As Long
    Dim rotationMaxVr As Long
    Dim minutiaeColor As Long
    Dim minutiaeMatchColor As Long
    Dim segmentsColor As Long
    Dim segmentsMatchColor As Long
    Dim directionsColor As Long
    Dim directionsMatchColor As Long
    Dim ok As Boolean

    Do
        ' get current identification/verification parameters
        GrGetIdentifyParameters thresholdId, rotationMaxId, GR_DEFAULT_CONTEXT
        GrGetVerifyParameters thresholdVr, rotationMaxVr, GR_DEFAULT_CONTEXT
        ' set current identification/verification parameters on options form
        Call formOptions.setParameters(thresholdId, rotationMaxId, thresholdVr, rotationMaxVr)

        ok = True
        ' show form with match options and biometric display options
        ' and get new parameters
        If Not (formOptions.getParameters(thresholdId, rotationMaxId, thresholdVr, rotationMaxVr, _
                    minutiaeColor, minutiaeMatchColor, segmentsColor, segmentsMatchColor, directionsColor, directionsMatchColor)) Then
            Exit Sub
        End If
        If ((thresholdId < GR_MIN_THRESHOLD) Or _
         (thresholdId > GR_MAX_THRESHOLD) Or _
         (rotationMaxId < GR_ROT_MIN) Or _
         (rotationMaxId > GR_ROT_MAX)) Then
            MsgBox ("Invalid identify parameters values!")
            ok = False
        End If
        If (thresholdVr < GR_MIN_THRESHOLD Or _
         thresholdVr > GR_MAX_THRESHOLD Or _
         rotationMaxVr < GR_ROT_MIN Or _
         rotationMaxVr > GR_ROT_MAX) Then
            MsgBox ("Invalid verify parameters values!")
            ok = False
        End If
        ' set new identification parameters
        If ok Then
            ret = GrSetIdentifyParameters(thresholdId, rotationMaxId, GR_DEFAULT_CONTEXT)
            ' error?
            If ret = GR_DEFAULT_USED Then
                MsgBox ("Invalid identify parameters values. Default values will be used.")
                ok = False
            End If
            ' set new verification parameters
            ret = GrSetVerifyParameters(thresholdVr, rotationMaxVr, GR_DEFAULT_CONTEXT)
            ' error?
            If ret = GR_DEFAULT_USED Then
                MsgBox ("Invalid verify parameters values. Default values will be used.")
                ok = False
            End If
            ' if everything ok
            If ok Then
                ' accept new parameters
                Call formOptions.AcceptChanges
                ' set new colors
                Call GrSetBiometricDisplayColors(minutiaeColor, minutiaeMatchColor, segmentsColor, segmentsMatchColor, directionsColor, directionsMatchColor)
                Exit Sub
            End If
        End If
    Loop
End Sub

' Display GrFinger version
Private Sub menuVersion_Click()
    Call MessageVersion
End Sub

' -----------------------------------------------------------------------------------
' Event support routines
' -----------------------------------------------------------------------------------

' Status event polling routine
Private Sub tmrStatus_Timer()
  ' Checks wether an status event occurred and, if so,
  ' calls consumer Sub to handle event data.
  If fireStatus = True Then
    ConsumeStatusEvent
    ' Returning event signal to false.
    fireStatus = False
  End If
End Sub

' Finger event polling routine
Private Sub tmrFinger_Timer()
  ' Checks wether a finger event occurred and, if so,
  ' calls consumer Sub to handle event data.
  If fireFinger = True Then
    ConsumeFingerEvent
    ' Returning event signal to false.
    fireFinger = False
  End If
End Sub

' Image event polling routine
Private Sub tmrImage_Timer()
  ' Checks wether an image event occurred and, if so,
  ' calls consumer Sub to handle event data.
  If fireImage = True Then
    ConsumeImageEvent
    ' Returning  event signal to false.
    fireImage = False
  End If
End Sub
